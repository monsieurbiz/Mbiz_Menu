<?php
/**
 * This file is part of Mbiz_Menu for Magento.
 *
 * @license GPL-3.0
 * @author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com> <@jacquesbh>
 * @category Mbiz
 * @package Mbiz_Menu
 * @copyright Copyright (c) 2016 Monsieur Biz (https://monsieurbiz.com/)
 */

/**
 * Page_Html_Topmenu Block
 * @package Mbiz_Menu
 */
class Mbiz_Menu_Block_Page_Html_Topmenu extends Mage_Core_Block_Template
{

    /**
     * Categories
     * @var array
     */
    protected $_categories;

    /**
     * @inheritDoc
     */
    public function getCacheLifetime()
    {
        return parent::getCacheLifetime() !== null
            ? parent::getCacheLifetime()
            : 259200; //72h in seconds
    }

    /**
     * @inheritdoc
     */
    public function getCacheKeyInfo()
    {
        return parent::getCacheKeyInfo() + [
            'category_id' => $this->getCurrentCategoryId(),
        ];
    }

    /**
     * Retrieve current category id
     *
     * @return int 0 is returned of no category found
     */
    public function getCurrentCategoryId()
    {
        if ($currentCategory = Mage::registry('current_category')) {
            return (int) $currentCategory->getId();
        }
        return 0;
    }

    /**
     * Give all categories of the current store
     * @return array Array of all categories
     */
    public function getCategories()
    {
        if (null === $this->_categories) {

            $collection = Mage::getModel('catalog/category')->getCollection();

            $pathLike = Mage_Catalog_Model_Category::TREE_ROOT_ID . '/' . Mage::app()->getStore()->getRootCategoryId() . '/%';
            $collection
                ->initCache(Mage::app()->getCacheInstance(), self::class, [Mage_Catalog_Model_Category::CACHE_TAG])
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addFieldToFilter('path', [
                    'like' => $pathLike,
                ])
                ->addIsActiveFilter()
                ->addAttributeToFilter('include_in_menu', 1)
                ->joinUrlRewrite();

            // Sort
            $collection->getSelect()->reset(Zend_Db_Select::ORDER);
            $collection
                ->addOrder('position', Varien_Data_Collection_Db::SORT_ORDER_ASC);

            foreach ($collection as $id => $category) {
                $this->_categories[$id] = $category;
            }
        }

        return $this->_categories;
    }

    /**
     * Retrieve the categories tree
     * @return array Array of the main categories with their children
     */
    public function getCategoriesTree()
    {
        $allCategories = $this->getCategories();
        $topCategories = [];

        $currentCategory = null;

        // Building tree from categories collection
        foreach ($allCategories as $idCat => $category) {

            // If level, we put category in an array with only top level categories.
            $topLevel = 2; // 1 is root category
            if ($category->getLevel() == $topLevel && !isset($topCategories[$idCat])) {
                $topCategories[$idCat] = $category;
            } else {
                // Foreach categories with sub category we add a child to the parent
                if (!isset($allCategories[$category->getParentId()])) {
                    // Ignore if no parent
                    continue;
                }
                $parent           = $allCategories[$category->getParentId()];
                $children         = $parent->getData('_children') ?: [];
                $children[$idCat] = $category;
                $category->setData('_parent', $parent);
                $parent->setData('_children', $children);
            }

            if ((int) $idCat === $this->getCurrentCategoryId()) {
                $currentCategory = $category;
            }
        }

        // Set current path
        if (null !== $currentCategory) {
            $setCategoryAsCurrent = function (Mage_Catalog_Model_Category $category, $isParent = false) use (&$setCategoryAsCurrent) {
                if ($isParent) {
                    $category->setContainsCurrentCategory(true);
                } else {
                    $category->setIsCurrentCategory(true);
                }
                if ($parentCategory = $category->getData('_parent')) {
                    $setCategoryAsCurrent($parentCategory, true);
                }
            };
            $setCategoryAsCurrent($currentCategory);
        }

        return $topCategories;
    }

}

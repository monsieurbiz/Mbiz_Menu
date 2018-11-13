# Mbiz_Menu

`Mbiz_Menu` is a Magento 1 module which provides an optimized menu as a simple block.

## Usage

The block `mbiz_menu/page_html_topmenu` gives 2 methods:

1. `getCategoriesTree()` which returns main categories with children.
    Children are available using the method `$cat->get_children()`.
2. `getCategories` which returns all the categories available in the menu.

Each category has 2 new data available:

* `contains_current_category` is set to `true` if the category has the current category in its children.
* `is_current_category` is set to `true` if the category is the actual current one.

### Example

No template is provided. But here is an really simple example:

```php
<?php
/* @var $this Mbiz_Menu_Block_Page_Html_Topmenu */
$_tree   = $this->getCategoriesTree();
$_output = $this->helper('catalog/output');
?>
<ul>
<?php foreach ($_tree as $_mainCategory): ?>
    <li class="<?php if ($_mainCategory->getIsCurrentCategory() || $_mainCategory->getContainsCurrentCategory()): ?>active<?php endif; ?>">
        <a href="<?php echo $_mainCategory->getUrl(); ?>">
            <?php echo $_output->categoryAttribute($_mainCategory, $_mainCategory->getName(), 'name'); ?>
        </a>
        <?php if ($_children = $_mainCategory->get_children()): ?>
        <ul>
            <?php foreach ($_children as $_child): ?>
            <li class="<?php if ($_child->getIsCurrentCategory()): ?>current<?php endif; ?>">
                <a href="<?php echo $_child->getUrl(); ?>">
                    <?php echo $_output->categoryAttribute($_child, $_child->getName(), 'name'); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
```

By default the block uses a 72 hours cache lifetime. Of course the default value can be overrided.

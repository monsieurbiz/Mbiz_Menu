# Mbiz_Menu

`Mbiz_Menu` is a Magento 1 module which provides an optimized menu as a simple block.

The block `mbiz_menu/page_html_topmenu` gives 2 methods:

* `getCategoriesTree()` which returns main categories with children.
    Children are available using the method `$cat->get_children()`.
* `getCategories` which returns all the categories available in the menu.

No template is provided. But here is an really simple example:

```
<?php
/* @var $this Mbiz_Menu_Block_Page_Html_Topmenu */
$_tree = $this->getCategoriesTree();
$_output = $this->helper('catalog/output');
?>
<ul>
<?php foreach ($_tree as $_mainCategory): ?>
    <li>
        <a href="<?php echo $_mainCategory->getUrl(); ?>">
            <?php echo $_output->categoryAttribute($_mainCategory, $_mainCategory->getName(), 'name'); ?>
        </a>
        <?php if ($_children = $_mainCategory->get_children()): ?>
        <ul>
            <?php foreach ($_children as $_child): ?>
            <li>
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

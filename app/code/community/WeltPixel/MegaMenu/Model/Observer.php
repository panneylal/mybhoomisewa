<?php

class WeltPixel_MegaMenu_Model_Observer extends Mage_Catalog_Model_Observer
{
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $menuBlock, $addTags = false)
    {
        $categoryModel = Mage::getModel('catalog/category');
        $mediaCategoryUrl = Mage::getBaseUrl('media') . 'catalog' . '/' . 'category' . '/';
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $categoryModel->load($category->getId());

            if ($addTags) {
                $menuBlock->addModelTags($categoryModel);
            }

            $tree = $parentCategoryNode->getTree();
			
            $customLink = $categoryModel->getWpCustomLink();
            $categoryData = array(
                'name'                      => $category->getName(),
                'right_block'               => $categoryModel->getWpCatRightBlock(),
                'top_block'                 => $categoryModel->getWpCatTopBlock(),
                'bottom_block'              => $categoryModel->getWpCatBottomBlock(),
                'columns'                   => $categoryModel->getWpNoColumns(),
                'static_block'              => $categoryModel->getWpStaticBlocks(),
                'title_color'               => $categoryModel->getWpTitleColor(),
                'title_hover_color'         => $categoryModel->getWpTitleHoverColor(),
                'title_image'               => ($categoryModel->getWpTitleImage()) ? $mediaCategoryUrl . $categoryModel->getWpTitleImage() : null,
                'header_bg_color'           => $categoryModel->getWpHeaderBgColor(),
                'header_bg_hover_color'     => $categoryModel->getWpHeaderBgHoverColor(),
                'content_bg_image'          => ($categoryModel->getWpContentBgImage()) ? $mediaCategoryUrl . $categoryModel->getWpContentBgImage() : null,
                'content_bg_img_dm'         => $categoryModel->getWpContentBgImgDm(),
                'content_bg_color'          => $categoryModel->getWpContentBgColor(),
                'id'                        => $nodeId,
                'url'                       => isset($customLink) ? $customLink : Mage::helper('catalog/category')->getCategoryUrl($category),
                'is_active'                 => $this->_isActiveMenuCategory($category),
                'display_mode'              => $categoryModel->getWpDisplayMode()
            );
            $categoryModel->unsetData();
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            $flatHelper = Mage::helper('catalog/category_flat');
            if ($flatHelper->isEnabled() && $flatHelper->isBuilt(true)) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode, $menuBlock, $addTags);
        }
    }
}

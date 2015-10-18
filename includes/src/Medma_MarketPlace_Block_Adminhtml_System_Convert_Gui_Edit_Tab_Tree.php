<?php
/**
 * Medma Marketplace
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Team
 * that is bundled with this package of Medma Infomatix Pvt. Ltd.
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Contact us Support does not guarantee correct work of this package
 * on any other Magento edition except Magento COMMUNITY edition.
 * =================================================================
 * 
 * @category    Medma
 * @package     Medma_MarketPlace
**/
class Medma_MarketPlace_Block_Adminhtml_System_Convert_Gui_Edit_Tab_Tree extends Mage_Adminhtml_Block_Widget_Form
{
		public function getCategoriesRecursively($categories)
		{
			$array='';
			$array= '<ul>';
			foreach($categories as $category) 
			{
			    $cat = Mage::getModel('catalog/category')->load($category->getId());
			    $count = $cat->getProductCount();
			    $array .= '<li><div class="category-mapping"><div class="category-name">'.$category->getName().' (ID: '.$category->getId().') </div></div>';
			    if($category->hasChildren()) 
			    {
					$children = Mage::getModel('catalog/category')->getCategories($category->getId());
			        $array .=  $this->getCategoriesRecursively($children);
			    }
				$array .= '</li>';
			}
			return  $array . '</ul>';
		}

		public function getStores()
		{
			$allStores = Mage::app()->getStores();
			$stores = array();
			$rootId = array();
			foreach($allStores as $store)
			{
				$rootCat =	Mage::app()->getStore($store->getId())->getRootCategoryId();
				if(!in_array($rootCat,$rootId))
				{
					$stores[] = $store->getId();
					$rootId[] = $rootCat;
				}
			}
			return $stores;
		}
		
		public function getRootCategories($eachStoreId)
		{
			$storeId 			= Mage::app()->getStore($eachStoreId)->getId();
			$rootCat 			=	Mage::app()->getStore($storeId)->getRootCategoryId();
			$_categories 	= Mage::getModel('catalog/category')->getCategories($rootCat);
			return $_categories;
		}
}

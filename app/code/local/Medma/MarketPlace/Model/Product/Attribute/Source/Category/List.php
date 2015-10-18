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
class Medma_MarketPlace_Model_Product_Attribute_Source_Category_List
{
	public function getOptions()
    {	
		$store = Mage::getModel('core/store')->load(Mage_Core_Model_App::DISTRO_STORE_ID);
		$rootId = $store->getRootCategoryId();
		$category_array = array();
		$this->_getCategory($category_array, $rootId);
		return $category_array;
	}
	
	protected function _getCategory(&$category_array, $categoryId)
	{
		$categories = Mage::getModel('catalog/category')->getCategories($categoryId);
		
		foreach($categories as $category) {
			$category_array[$category->getId()] = str_repeat("-", (($category->getLevel() - 2) * 2)). ' ' . $category->getName();
			
			if($category->hasChildren())
				$this->_getCategory($category_array, $category->getId());
		}
	}
}
?>

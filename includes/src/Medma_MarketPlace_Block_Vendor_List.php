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

class Medma_MarketPlace_Block_Vendor_List extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('marketplace/vendor/list.phtml');
        parent::__construct();
    }
    
    public function getVendorListCollection()
    {
		$userCollection = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1);		
				
		$userCollection->getSelect()->join(
			array('user_profile' => Mage::getSingleton('core/resource')->getTableName('marketplace/profile')), 
			'user_profile.user_id = main_table.user_id', array('user_profile.*')
		);		
		
        return $userCollection;
	}
	
	public function getProductList($vendor_id)
	{
		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('vendor', $vendor_id)
			->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->addAttributeToFilter('approved', Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_YES)
			->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))			
			->setPageSize(4);
			
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);		
		
		return $collection;
	}
	
	public function getProductImage($product_id)
	{
		$productModel = Mage::getModel('catalog/product')->load($product_id);			
		return Mage::helper('catalog/image')->init($productModel, 'small_image')->resize(91, 91);
	}
	
	public function getVendorProfileUrl($vendorId) 
	{
        return $this->getUrl('marketplace/vendor/profile', array('id' => $vendorId));
    }
    
    public function getViewAllProduct($vendorId)
    {
		return $this->getUrl('marketplace/vendor/items', array('id' => $vendorId));
	}
	
	public function getVendorImageUrl($vendorImageName)
	{
		$dirPath = 'vendor' . DS . 'images';
		return Mage::helper('marketplace')->getImagesUrl($dirPath) . $vendorImageName;
	}
}

?>

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
class Medma_MarketPlace_Block_Vendor_Profile_Items extends Mage_Catalog_Block_Product_List {

	public function getVendorProductCollection()
	{
		$vendorProductCollection = $this->_getProductCollection();
				
		return $vendorProductCollection;
	}
	
	protected function _getProductCollection()
    {
		$profileCollection = Mage::getModel('marketplace/profile')->getCollection();
				
		$profileCollection->getSelect()
			->reset(Zend_Db_Select::COLUMNS)
			->columns('user_id');
		
		$userIds = $profileCollection->toArray(array('user_id'));
		
		$userCollection = Mage::getModel('admin/user')
			->getCollection()
			->addFieldToFilter('is_active', 1);
			
		if($userIds['totalRecords'] != 0)
			$userCollection->addFieldToFilter('user_id', array('in' => $userIds));
			
		$userIds = $userCollection->getAllIds();
			
		$collection = parent::_getProductCollection();
				
		$collection->addAttributeToSelect('vendor')			
			->addAttributeToFilter('approved', Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_YES)
			->addAttributeToFilter('vendor', array(array('in' => $userIds), array('null' => true)));
			
		if($this->getRequest()->getRouteName() == 'marketplace' && 
			$this->getRequest()->getControllerName() == 'vendor' && 
			$this->getRequest()->getActionName() == 'items') 
		{
			$profileId = $this->getRequest()->getParam('id');
			
			if(isset($profileId))
			{
				$vendorId = Mage::getModel('marketplace/profile')->load($profileId)->getUserId();	
			
				$collection->addAttributeToSelect('vendor')
				->addAttributeToFilter('approved', 1)
					->addAttributeToFilter('vendor', $vendorId);
			}
		}		

        return $collection;
    }
	    
}

?>

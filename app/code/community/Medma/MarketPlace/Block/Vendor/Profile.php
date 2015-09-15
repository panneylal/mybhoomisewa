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
class Medma_MarketPlace_Block_Vendor_Profile extends Mage_Core_Block_Template {

    public function getVendorInfo() {
        $profileId = $this->getRequest()->getParam('id');
        return Mage::getModel('marketplace/profile')->load($profileId);
    }

    public function getUserObject($userId) {
        return Mage::getModel('admin/user')->load($userId);
    }

    public function getAddFavouriteUrl($vendorId) {
        return $this->getUrl('marketplace/favourite/add', array('id' => $vendorId));
    }

    public function getCountryName($countryCode) {
        return Mage::helper('marketplace')->getCountryName($countryCode);
    }

    public function getVendorProfileUrl($vendorId) {
        return $this->getUrl('marketplace/vendor/profile', array('id' => $vendorId));
    }
    
    public function getVendorItemsUrl($profileId) {
        return $this->getUrl('marketplace/vendor/items', array('id' => $profileId));
    }
    
    public function getMessage($vendorInfo, $userObject)
    {
        $message = $this->getVendorInfo()->getMessage();
        if(trim($message) != '')        
            return $message;    
        else
            return ($this->__('Based in ') .
                $this->getCountryName($vendorInfo->getCountry()) . ' ' .
                $vendorInfo->getShopName() . ' has been member since ' .
                date("M j, Y", strtotime($userObject->getCreated())));
    }
    
    public function getVendorRating()
    {
		$ratingCollection = Mage::getModel('marketplace/rating')->getCollection()			
			->setOrder('sort_order', 'ASC');
			
		$vendorId = $this->getVendorInfo()->getUserId();
		
		$productIds = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToFilter('status', 1)
			->addAttributeToFilter('approved', Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_YES)
			->addAttributeToFilter('vendor', $vendorId)
			->getAllIds();
			
		$invoiceItemIds = Mage::getModel('sales/order_invoice_item')
			->getCollection()
			->addFieldToFilter('product_id', array('in' => $productIds))
			->getAllIds();
			
		$ratingValues = array();
			
		foreach($ratingCollection as $rating)
		{
			$rateCollection = Mage::getModel('marketplace/rate')
				->getCollection()
				->addFieldToSelect('rating_id')
				->addFieldToSelect('value')
				->addFieldToFilter('invoice_item_id', array('in' => $invoiceItemIds))
				->addFieldToFilter('rating_id', $rating->getId());
			
			$rateCollection->getSelect()
				->columns('SUM(value) as total, ((SUM(value)  * 100)/ (COUNT(*) * 5)) AS value, COUNT(*) as records')
				->group('rating_id');
				
			if($rateCollection->count())
			{
				$rateFinalModel = $rateCollection->getFirstItem();
				
				$ratingValues[$rating->getId()] = array(
					'value' => $rateFinalModel->getValue(),
					'total' => $rateFinalModel->getTotal(),
					'records' => $rateFinalModel->getRecords(),
					'name' => $rating->getName(),
				);
			}
			else
			{
				$ratingValues[$rating->getId()] = array(
					'value' => 0,
					'total' => 0,
					'records' => 0,
					'name' => $rating->getName(),
				);
			}
		}
		
		return $ratingValues;
	}
	
	public function getReviewCount($vendorId, $type)
	{	
		$productIds = Mage::getModel('catalog/product')->getCollection()
			->addFieldToFilter('vendor', $vendorId)->getAllIds();
			
		$invoiceItemIds = Mage::getModel('sales/order_invoice_item')->getCollection()
			->addFieldToFilter('product_id', array('in' => $productIds))->getAllIds();
						
		$reviewModel = Mage::getModel('marketplace/review')->getCollection()
				->addFieldToFilter('invoice_item_id', array('in' => $invoiceItemIds))
				->addFieldToFilter('status', 1)
				->addFieldToFilter('type', $type);
				
		return str_pad($reviewModel->count(), 3, "0", STR_PAD_LEFT);
	}
	
	public function getTypeUrl($vendorId, $linkType)
	{
		return Mage::getUrl('marketplace/vendor/profile', array('id' => $vendorId, 'type'=> $linkType));
	}
}

?>

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
class Medma_MarketPlace_Block_Catalog_Product_Reference_List extends Mage_Core_Block_Template
{
	protected $_product = null;

    function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
    
    public function getReferenceProducts()
    {
		$mainProductId = $this->getProduct()->getId();
		
		$referenceProductCollection = Mage::getModel('marketplace/vendor_product_reference')->getCollection()
			->addFieldToFilter('product_id', $mainProductId);
			
		if($referenceProductCollection->count())
		{
			if($parentProductId = $referenceProductCollection->getFirstItem()->getParentId())
			{
				$referenceProductCollection = Mage::getModel('marketplace/vendor_product_reference')->getCollection()
					->addFieldToFilter('parent_id', $parentProductId);
					
				$referenceProductCollection->getSelect()
					->reset(Zend_Db_Select::COLUMNS)
					->columns('product_id');
					
				$productIds = $referenceProductCollection->getData();
				
				foreach($productIds as $index => $productId)
				{
					if($productId['product_id'] == $mainProductId)
					{
						$productIds[$index]['product_id'] = $parentProductId;
						break;
					}
				}
				
				return Mage::getResourceModel('catalog/product_collection')
					->addAttributeToFilter('entity_id', array('in' => $productIds))
					->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
					->addAttributeToFilter('approved', 1)
					->addAttributeToSelect('*');
			}
			else
			{
				$referenceProductCollection = Mage::getModel('marketplace/vendor_product_reference')->getCollection()
					->addFieldToFilter('parent_id', $mainProductId);
					
				$referenceProductCollection->getSelect()
					->reset(Zend_Db_Select::COLUMNS)
					->columns('product_id');
					
				$productIds = $referenceProductCollection->getData();
				
				return Mage::getResourceModel('catalog/product_collection')
					->addAttributeToFilter('entity_id', array('in' => $productIds))
					->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
					->addAttributeToFilter('approved', 1)
					->addAttributeToSelect('*');
			}
		}
	}
	
	public function getVendorInfo($userId) {

        $vendorProfileCollection = Mage::getModel('marketplace/profile')->getCollection()
                ->addFieldToFilter('user_id', $userId);
                
        $userCollection = Mage::getModel('admin/user')->getCollection()
                ->addFieldToFilter('user_id', $userId);

        if ($vendorProfileCollection->count() && $userCollection->count())
            return $vendorProfileCollection->getFirstItem();
        else
            return null;
    }

    public function getVendorProfileUrl($vendorId) {
        return $this->getUrl('marketplace/vendor/profile', array('id' => $vendorId));
    }
    
    public function getFormattedPrice($finalPrice)
    {
		return Mage::helper('core')->currency($finalPrice, true, false);
	}

}

?>

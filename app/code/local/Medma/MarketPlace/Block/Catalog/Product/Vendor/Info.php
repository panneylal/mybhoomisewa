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
class Medma_MarketPlace_Block_Catalog_Product_Vendor_Info extends Mage_Core_Block_Template {

    public function getProduct() {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }

    public function getVendorInfo() {
        $userId = $this->getProduct()->getVendor();

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

    public function getAddFavouriteUrl($vendorId) {
        return $this->getUrl('marketplace/favourite/add', array('id' => $vendorId));
    }

    public function getProductId() {
        return (int) $this->getRequest()->getParam('id');
    }

}

?>

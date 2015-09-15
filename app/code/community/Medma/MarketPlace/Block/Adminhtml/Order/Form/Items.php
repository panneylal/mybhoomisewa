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
class Medma_MarketPlace_Block_Adminhtml_Order_Form_Items extends Mage_Adminhtml_Block_Template {

    public function __construct() {
        $this->setTemplate('marketplace/sales/order/view/form/items.phtml');
        parent::__construct();
    }

    public function getOrder() {
        return Mage::registry('current_order');
    }

    public function getItemsCollection() {
        return $this->getOrder()->getItemsCollection();
    }

    public function getProductIdsCollection() 
    {
        $roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
        // $role = Mage::getModel('admin/roles')->load($roleId);

        $current_user = Mage::getSingleton('admin/session')->getUser();

        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('status', 1);

        if ($current_user->getRole()->getRoleId() == $roleId)
            $collection->addAttributeToFilter('vendor', $current_user->getId());

        return $collection->getAllIds();
    }

    public function getAdminCommission($item)
    {
        return $item->getCommissionAmount();
    }

    public function getVendorAmount($item)
    {   
        $total_price = ($item->getPriceInclTax() * $item->getQtyOrdered());
        return $total_price - $item->getCommissionAmount();
    }
}

?>

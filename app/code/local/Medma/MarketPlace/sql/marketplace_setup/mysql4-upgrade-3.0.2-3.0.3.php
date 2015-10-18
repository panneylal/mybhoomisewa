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

Mage::getConfig()->reinit();
Mage::app()->reinitStores();
$productModel = Mage::getModel('catalog/product');

$enableFilterProduct = $productModel->getCollection()->addAttributeToFilter('status', 1);
$disableFilterProduct = $productModel->getCollection()->addAttributeToFilter('status', 2);
	
foreach($enableFilterProduct as $product)
	$product->setApproved(Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_YES)->save();
	
foreach($disableFilterProduct as $product)
	$product->setApproved(Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_NO)->save();

?>

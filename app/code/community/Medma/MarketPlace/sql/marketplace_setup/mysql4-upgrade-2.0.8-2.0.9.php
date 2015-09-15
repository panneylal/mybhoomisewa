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
$data = array(
	'group' => 'Email',
	'code' => 'notify_new_order_email',
	'name' => 'notify_new_order_email',
	'type' => 'select',
	'label' => 'Notify New Order Email to Me',
	'title' => 'Notify New Order Email to Me',
	'source_model' => 'adminhtml/system_config_source_yesno',
);

Mage::getModel('marketplace/configuration')->setData($data)->save();

?>

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
$roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
$role = Mage::getModel('admin/roles')->load($roleId);
$userIds = Mage::getResourceModel('admin/roles')->getRoleUsers($role);

foreach($userIds as $userId)
{
	$configDataCollection = Mage::getModel('marketplace/configuration_data')
		->getCollection()
		->addFieldToFilter('vendor_id', $userId);
		
	if($configDataCollection->count())
	{
		$configDataObject = $configDataCollection->getFirstItem();
		$config_data = json_decode($configDataObject->getValue(), true);
		$config_data['notify_new_order_email'] = 1;
		$configDataObject->setValue(json_encode($config_data))->save();
	}
	else
	{
		Mage::getModel('marketplace/configuration_data')
			->setVendorId($userId)
			->setValue(
				json_encode(array('notify_new_order_email' => 1))
			)->save();
	}			
}
?>

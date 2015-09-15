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
$roleId = Mage::getStoreConfig('marketplace/general/vendor_role');
if(!isset($roleId))
{
	$roleCollection = Mage::getModel('admin/roles')
        ->getCollection()
        ->addFieldToFilter('role_name', Medma_MarketPlace_Model_Vendor::ROLE);

	if ($roleCollection->count() > 0)
		$roleId = $roleCollection->getFirstItem()->getId();
}

$rulesCollection = Mage::getResourceModel('admin/rules_collection')
	->getByRoles($roleId)
	->load();

$rulesArray = array();
	
foreach ($rulesCollection->getItems() as $item)
{
	$itemResourceId = $item->getResource_id();
	
	if ($item->getPermission() == 'allow')
		$rulesArray[] = $itemResourceId;
}

$rulesArray[] = 'admin/vendor/configuration';

Mage::getModel('admin/rules')
	->setRoleId($roleId)
	->setResources($rulesArray)
	->saveRel();
?>

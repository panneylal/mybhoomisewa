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
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
	->newTable($installer->getTable('marketplace/configuration_data'))
		->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
			 ), 'Id')		
		->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'nullable' => false,				
			), 'Vendor Id')
		->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Value');		

$installer->getConnection()->createTable($table);	
?>

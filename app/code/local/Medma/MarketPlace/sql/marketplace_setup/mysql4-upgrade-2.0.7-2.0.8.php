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
	->newTable($installer->getTable('marketplace/configuration'))
		->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
			 ), 'Id')		
		->addColumn('group', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Group')
		->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Code')
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Name')
		->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Type')
		->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Label')
		->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,				
			), 'Title')
		->addColumn('class', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => true,				
			), 'Class')		
		->addColumn('style', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => true,				
			), 'Style')		
		->addColumn('options', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => true,				
			), 'Options')
		->addColumn('source_model', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => true,				
			), 'Source Model')
		->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => true,				
			), 'Value')
		->addColumn('after_element_html', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => true,				
			), 'After Element Html')
		->addColumn('disable', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
				'default' => 0,
				'nullable' => true,				
			), 'Disable')
		->addColumn('readonly', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
				'default' => 0,
				'nullable' => true,				
			), 'Readonly')
		->addColumn('required', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
				'default' => 0,
				'nullable'  => true,
			), 'Required');		

$installer->getConnection()->createTable($table);
?>

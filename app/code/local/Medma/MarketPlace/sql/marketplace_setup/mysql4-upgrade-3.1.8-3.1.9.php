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
*/
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()->newTable($installer->getTable('marketplace/vendor_product_reference'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'Id')
        ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Parent Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Product Id');
$installer->getConnection()->createTable($table);
$installer->endSetup();
?>

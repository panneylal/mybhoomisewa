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

class Medma_MarketPlace_Model_System_Config_Source_ShopInfo
{
	protected function _construct() 
	{
		$this->_init('marketplace/system_config_source_shopInfo');	
	}
    
	public function toOptionArray()
    {
		return array(
			'left' => Mage::helper('adminhtml')->__('Left Sidebar'),
			'right' => Mage::helper('adminhtml')->__('Right Sidebar'),
			'product_info' => Mage::helper('adminhtml')->__('Product Info')
		);
    }    
}
?>

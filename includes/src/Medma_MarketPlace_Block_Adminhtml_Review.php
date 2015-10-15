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
class Medma_MarketPlace_Block_Adminhtml_Review extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{		
		$this->_controller = 'adminhtml_review';
		$this->_blockGroup = 'marketplace';
		if(Mage::getSingleton('core/session')->getReviewType() != Medma_MarketPlace_Model_Review::PENDING) 
			$this->_headerText = 'All Reviews';
		else
			$this->_headerText = 'Pending Review';					
		parent::__construct();
		$this->_removeButton('add');
	}
}
?>

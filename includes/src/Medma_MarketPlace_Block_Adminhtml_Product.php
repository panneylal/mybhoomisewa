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
class Medma_MarketPlace_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'marketplace';
        $vendor_id = $this->getRequest()->getParam('id');
        if(isset($vendor_id))
			$this->_headerText = Mage::helper('marketplace')->__('Mange Products');
		else
			$this->_headerText = Mage::helper('marketplace')->__('Pending Products');
        
		$data = array(
			'label' =>  'Back',
			'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_vendor/index/') . '\')',
			'class'     =>  'back'
		);
		
		$this->addButton ('my_back', $data, 0, 100,  'header');
		
        parent::__construct();
        
        $this->_removeButton('add');
        
       
    }

}

?>

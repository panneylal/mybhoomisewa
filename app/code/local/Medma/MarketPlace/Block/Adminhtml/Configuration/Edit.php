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
class Medma_MarketPlace_Block_Adminhtml_Configuration_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_objectId = 'entity_id';
		$this->_blockGroup = 'marketplace';
		$this->_controller = 'adminhtml_configuration';
				
		parent::__construct();
		
		$this->_updateButton('save', 'label', 'Save Configuration');
		
		$current_user = Mage::getSingleton('admin/session')->getUser();        
        $roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');        
		if ($current_user->getRole()->getRoleId() == $roleId)
			$this->_removeButton('back');		
		else
		{
			$this->_updateButton('back', null, array
				(
					'label'=> Mage::helper('marketplace')->__('Back'),
					'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_vendor/index/') . '\')', 
					'class'=> 'back',
				)
			);
		}
		
		$this->_removeButton('reset');		
	}
	
	public function getHeaderText()
	{
		return $this->__('Configuration');		
	}
}
?>

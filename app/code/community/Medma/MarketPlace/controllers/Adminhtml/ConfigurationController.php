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
class Medma_MarketPlace_Adminhtml_ConfigurationController extends Mage_Adminhtml_Controller_Action 
{
	protected function _isAllowed()
	{
		return true;
	}
	
	protected function _initAction() 
	{
        $this->loadLayout()->_setActiveMenu('vendor/configuration');
        return $this;
    }

    public function indexAction() 
    {		
		$roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
        $current_user = Mage::getSingleton('admin/session')->getUser();

        if ($current_user->getRole()->getRoleId() != $roleId) {
            $this->_forward('empty');
            return;
        }
        
        $vendorId = $current_user->getUserId();
        
        $configDataCollection = Mage::getModel('marketplace/configuration_data')
			->getCollection()
			->addFieldToFilter('vendor_id', $vendorId);
		
		if($configDataCollection->count())
		{
			$configDataObject = $configDataCollection->getFirstItem();
			$config_data = json_decode($configDataObject->getValue(), true);
			Mage::register('config_data', $config_data);			
		}
		
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_configuration_edit'));
        $this->renderLayout();
    }
    
    public function vendorAction()
    {
		$vendorId = $this->getRequest()->getParam('id');
		
		$configDataCollection = Mage::getModel('marketplace/configuration_data')
			->getCollection()
			->addFieldToFilter('vendor_id', $vendorId);
		
		if($configDataCollection->count())
		{
			$configDataObject = $configDataCollection->getFirstItem();
			$config_data = json_decode($configDataObject->getValue(), true);
			Mage::register('config_data', $config_data);			
		}
		
		$this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_configuration_edit'));
        $this->renderLayout();
	}
    
    public function emptyAction() 
    {
        $this->loadLayout()->_setActiveMenu('medma/vendor/configuration');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_configuration_empty'));
        $this->renderLayout();
    }
    
    public function saveAction()
    {
		$postData = $this->getRequest()->getPost();
		
		unset($postData['form_key']);
		
		$current_user = Mage::getSingleton('admin/session')->getUser();		
		
		$userId = $this->getRequest()->getParam('id');		
		if(!isset($userId))
			$userId = $current_user->getUserId();
		
		$configDataCollection = Mage::getModel('marketplace/configuration_data')
			->getCollection()
			->addFieldToFilter('vendor_id', $userId);		
		
		if($configDataCollection->count())
		{
			$configDataObject = $configDataCollection->getFirstItem();		
			$configDataObject->setValue(json_encode($postData))->save();
		}
		else
		{
			Mage::getModel('marketplace/configuration_data')
				->setVendorId($userId)
				->setValue(json_encode($postData))
				->save();
		}
		Mage::getSingleton('adminhtml/session')->addSuccess('configuration successfully saved');
		
		$roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');        

        if ($current_user->getRole()->getRoleId() == $roleId)
			$this->_redirect('*/*/index');
		else
			$this->_redirect('*/*/vendor', array('id' =>  $userId));
	}
}
?>

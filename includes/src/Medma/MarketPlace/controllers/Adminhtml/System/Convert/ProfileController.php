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
require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'System/Convert' . DS . 'ProfileController.php');

class Medma_MarketPlace_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_System_Convert_ProfileController
{
	public function runAction()
    {
        $this->_initProfile();
        $this->loadLayout();
        $this->renderLayout();
        
        /**Fetch Current User Id**/    
	    $user = Mage::getSingleton('admin/session');
		$userName = $user->getUser()->getUsername();
        $filename = 'vendor/export_product_'.$userName.'.csv';
        if(file_exists($filename) && filesize($filename)!=0)
        {
		    $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			$content = file_get_contents($baseUrl.$filename);
			$this->_prepareDownloadResponse('export_product.csv', $content);
		}
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}

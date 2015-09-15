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
class Medma_MarketPlace_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getImagesDir($type)
	{
		$path = Mage::getBaseDir('media') . DS . 'marketplace' . DS . 'vendor' . DS . $type . DS;
		if(!is_dir($path))
			mkdir($path, 0777, true);
			
		return $path;
	}
	
	public function getImagesUrl($type)
	{
		return Mage::getBaseUrl('media') . 'marketplace' . DS . 'vendor' . DS . $type . DS;	
	}
	
	public function getCountryName($countryCode)
	{		
		return Mage::app()->getLocale()->getCountryTranslation($countryCode);
	}
	
	public function getConfig($group, $field)
	{
		return Mage::getStoreConfig('marketplace/' . $group . '/' . $field, Mage::app()->getStore());
	}
	
	public function getSellerRegistrationLabelConfig()
	{
		return $this->getConfig('vendor_registration', 'request_seller_label');
	}
	
	public function getVarificationProofTypeList()
	{
		$proofType[''] = '';
		
		$prooftypeCollection = Mage::getModel('marketplace/prooftype')->getCollection()->addFieldToFilter('status', 1);
		
		foreach($prooftypeCollection as $prooftype)		
			$proofType[$prooftype->getId()] = $prooftype->getName();
		
		return $proofType;
	}	
	
	/**Check current user is a vendor**/
	public function isVendor()
	{
		$roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
		$currentUser = Mage::getSingleton('admin/session')->getUser();

		if($currentUser->getRole()->getRoleId() == $roleId) 
			return true;

		return false;
	}
	
	public function switchTemplate()
	{
		$configValue = $this->getConfig('general', 'product_view_layout');
		switch($configValue)
		{
			case 'empty';
				return 'page/empty.phtml';				
			case 'one_column';
				return 'page/1column.phtml';
			case 'two_columns_left';
				return 'page/2columns-left.phtml';				
			case 'two_columns_right';
				return 'page/2columns-right.phtml';				
			case 'three_columns';
				return 'page/3columns.phtml';				
		}
		return 'page/1column.phtml';
	}
	
	public function getExtensionVersion()
	{
		$dirPath = 'images';
		$companyLogo =  Mage::helper('marketplace')->getImagesUrl($dirPath) . 'logo.png';
		
		$version = $this->__('<div style="color: #016599; border: 1px solid #CCCCCC;padding: 8px 6px; margin-bottom: 20px; background: #EAF2F5; border-radius:7px;"> <img src="%s" width="150px"/>
		<div style="margin:0 0 0 10px;">
        <strong>MarketPlace v%s by </strong><a style="color:#999999;" target="_blank" href="http://www.medma.net"><strong>Medma Infomatix</strong></a>
        </div>
        <div style="margin:0 0 0 10px;">
        <strong>Need Help? </strong><a style="color:#999999;" href="mailto:magento.support@medma.in"><strong>Email Us</strong></a>
        </div>
        
        </div>', $companyLogo , Mage::getConfig()->getNode()->modules->Medma_MarketPlace->version);
		
		return (string) $version ;
	}

}

?>

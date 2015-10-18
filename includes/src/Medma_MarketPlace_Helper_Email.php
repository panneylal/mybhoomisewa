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
class Medma_MarketPlace_Helper_Email extends Mage_Core_Helper_Abstract
{	
	public function isEmailAllow($group, $field)
	{
		return Mage::getStoreConfig('marketplace/' . $group . '/' . $field);
	}
		
	public function vendorRegistrationEmail($data, $uploaded_files, $userId)
	{	
		$templateId = Mage::helper('marketplace')->getConfig('vendor_registration_email', 'email_template');
		$identity = Mage::helper('marketplace')->getConfig('vendor_registration_email', 'email_receiver');		
		
		$uploadedFileString = '';
		if(count($uploaded_files))
		{
			$data['proof_value'] = $this->getProofValue($data['proof_type']);
			foreach($uploaded_files as $file)
				$uploadedFileString .= $this->getVerificationImage($file);
		}		
		else
		{
			$data['proof_value'] = 'No Proof';
			$uploadedFileString = 'N/A';
		}
		
		$data['uploaded_files'] = $uploadedFileString; Mage::helper('marketplace')->getCountryName($data['country']);
		$data['country_name'] = Mage::helper('marketplace')->getCountryName($data['country']);
		$data['email_id'] = $data['email'];
		$data['activate_url'] = Mage::helper('adminhtml')->getUrl('admin_marketplace/adminhtml_vendor/edit', array('id'=> $userId));		
		
		$this->sendEmail($templateId, $identity, $data);
	}
	
	public function sendEmail($templateId, $identity, $vars)
	{	
		$recepientName = Mage::getStoreConfig('trans_email/ident_' . $identity . '/name'); 
		$recepientEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email'); 
		$sender = array('name' => $vars['firstname'] . ' ' . $vars['lastname'], 'email' => $vars['email']);
		
		$storeId = Mage::app()->getStore()->getId();

		$translate  = Mage::getSingleton('core/translate');		
		Mage::getModel('core/email_template')->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

		$translate->setTranslateInline(true);		
	}
	
	public function getVerificationImage($image_name)
	{
		$dir_name = 'vendor' . DS . 'varifications';
        $dir_path = Mage::helper('marketplace')->getImagesUrl($dir_name);
        return '[ <a href="' . $dir_path . $image_name . '">' . $image_name . '</a> ] ' ;
	}
	
	public function getProofValue($id)
	{	
		$proofTypeObject = Mage::getModel('marketplace/prooftype')->load($id);
		return $proofTypeObject->getName();
	}
	
	public function vendorActivateEmail($data)
	{
		$templateId = Mage::helper('marketplace')->getConfig('vendor_activation_email', 'email_template');
		$identity = Mage::helper('marketplace')->getConfig('vendor_activation_email', 'email_sender');	
		
		$this->sendActivateVendorEmail($templateId, $identity, $data);
	}
	
	public function sendActivateVendorEmail($templateId, $identity, $vars)
	{	
		$senderName = Mage::getStoreConfig('trans_email/ident_' . $identity . '/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email');    
		$sender = array('name' => $senderName, 'email' => $senderEmail);
		
		$storeId = Mage::app()->getStore()->getId();
		
		$translate  = Mage::getSingleton('core/translate');
		
		Mage::getModel('core/email_template')->sendTransactional($templateId, $sender, $vars['email'], $vars['firstname'], $vars, $storeId);		
				 
		$translate->setTranslateInline(true);
	}	
	
	public function vendorConfirmationEmail($data)
	{
		$templateId = Mage::helper('marketplace')->getConfig('registration_confirmation_email', 'email_template');
		$identity = Mage::helper('marketplace')->getConfig('registration_confirmation_email', 'email_sender');	
		
		$this->sendConfirmationEmail($templateId, $identity, $data);
	}
	
	public function sendConfirmationEmail($templateId, $identity, $vars)
	{
		$senderName = Mage::getStoreConfig('trans_email/ident_' . $identity . '/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email');    
		$sender = array('name' => $senderName, 'email' => $senderEmail);
		
		$storeId = Mage::app()->getStore()->getId();
		
		$translate  = Mage::getSingleton('core/translate');
		
		Mage::getModel('core/email_template')->sendTransactional($templateId, $sender, $vars['email'], $vars['firstname'], $vars, $storeId);		
				 
		$translate->setTranslateInline(true);
	}
}
?>

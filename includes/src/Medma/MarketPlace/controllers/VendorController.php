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
class Medma_MarketPlace_VendorController extends Mage_Core_Controller_Front_Action
{
	public function registerAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function profileAction()
	{
		$vendorId = $this->getRequest()->getParam('id');
		$vendorObject = Mage::getModel('marketplace/profile')->load($vendorId);
		$userObject = Mage::getModel('admin/user')->load($vendorObject->getUserId());
		if(!$userObject->getIsActive())
			$this->_redirectUrl(Mage::getBaseUrl());
			
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function itemsAction()
	{
		$vendorId = $this->getRequest()->getParam('id');
		$vendorObject = Mage::getModel('marketplace/profile')->load($vendorId);
		$userObject = Mage::getModel('admin/user')->load($vendorObject->getUserId());
		if(!$userObject->getIsActive())
			$this->_redirectUrl(Mage::getBaseUrl());
			
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction()
	{		
		if ($data = $this->getRequest()->getPost())
		{	
            try 
            {
				$total_file_upload = $this->getRequest()->getParam('total_file_upload', false);
				
				$file_types = Mage::helper('marketplace')->getConfig('vendor_registration', 'files_allowed');
				$file_types_array = array_map('trim', split(',', $file_types));
				
				$max_allowed_file_size = Mage::helper('marketplace')->getConfig('vendor_registration', 'max_allowed_file_size');				
				$max_allowed_file_size_bytes = ($max_allowed_file_size * 1024 * 1024);				
				
				$uploaded_files = array();
				for($i = 1; $i <= $total_file_upload; $i++)
				{
					$file_control_name = 'varification_proof_' . $i;
					
					if (isset($_FILES[$file_control_name]['name']) && $_FILES[$file_control_name]['name'] != '')
					{
						if($_FILES[$file_control_name]['size'] > $max_allowed_file_size_bytes)
							throw new Exception('file size should not exceed ' . $max_allowed_file_size .  ' Mb');

						$uploader = new Varien_File_Uploader($file_control_name);
						$uploader->setAllowedExtensions($file_types_array);

						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);

						$dir_name = 'vendor' . DS . 'varifications';
						$dir_path = Mage::helper('marketplace')->getImagesDir($dir_name);
						
						try
						{
							$uploader->save($dir_path, $_FILES[$file_control_name]['name']);
						}
						catch(Exception $e)
						{
							throw new Exception('File type not allowd. Please upload (' . $file_types .  ')');
						}
						$uploaded_files[] = $_FILES[$file_control_name]['name'];
					}				
				}				
				
				$user = Mage::getModel('admin/user')->setData(array(
						'username' => $data['username'],
						'firstname' => $data['firstname'],
						'lastname' => $data['lastname'],
						'email' => $data['email'],
						'password' => $data['password'],
						'is_active' => 0
						))
					->save();					
				
				if ($this->getRequest()->getParam('password', false)) {
					$user->setNewPassword($this->getRequest()->getParam('password', false));
				}

				if ($this->getRequest()->getParam('confirmation', false)) {
					$user->setPasswordConfirmation($this->getRequest()->getParam('confirmation', false));
				}
					
				$result = $user->validate();
				if (is_array($result)) {
					foreach($result as $error) {
						Mage::getSingleton('core/session')->addError($error);
					}
					Mage::getSingleton('core/session')->setTestData($data);
					$this->_redirect('*/*/register');
					return;
				}
				
				$role_id = Mage::helper('marketplace')->getConfig('general', 'vendor_role');

				$user->setRoleIds(array($role_id))
					 ->setRoleUserId($user->getUserId())
					 ->saveRelations();
                
                $profile = Mage::getModel('marketplace/profile')
					->setTotalAdminCommission(0)
					->setTotalVendorAmount(0)
					->setTotalVendorPaid(0);
                        
                $profile->setUserId($user->getUserId())
					->setShopName($this->getRequest()->getParam('shop_name', false))
					->setContactNumber($this->getRequest()->getParam('contact_number', false))
					->setCountry($this->getRequest()->getParam('country', false))
					->setProofType($this->getRequest()->getParam('proof_type', false))
					->setVarificationFiles(json_encode($uploaded_files))
					->save();
					
				if(Mage::helper('marketplace/email')->isEmailAllow('vendor_registration_email', 'enable_registration_email'))
					Mage::helper('marketplace/email')->vendorRegistrationEmail($data, $uploaded_files, $user->getUserId());
					
				if(Mage::helper('marketplace/email')->isEmailAllow('registration_confirmation_email', 'enabled'))
					Mage::helper('marketplace/email')->vendorConfirmationEmail($data);
                
                Mage::getSingleton('core/session')->addSuccess('Request has been sent successfully, we will contact you soon.');
                        
                $this->_redirect('*/*/register', array('_secure' => true));
                return;
			} 
			catch (Exception $e) 
			{				
                Mage::getSingleton('core/session')->addError($e->getMessage());
				Mage::getSingleton('core/session')->setTestData($data);
                $this->_redirect('*/*/register', array('_secure' => true));
                return;
            }
		}
	}		
}

?>

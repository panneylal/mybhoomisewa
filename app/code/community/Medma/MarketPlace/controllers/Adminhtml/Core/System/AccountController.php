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
require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'System' . DS . 'AccountController.php');

class Medma_MarketPlace_Adminhtml_Core_System_AccountController extends Mage_Adminhtml_System_AccountController {

    public function saveAction()
    {
 $uploaded_files = array();
		$userId = Mage::getSingleton('admin/session')->getUser()->getId();

        $pwd = null;

        $user = Mage::getModel("admin/user")->load($userId);
        $user->setId($userId)
                ->setUsername($this->getRequest()->getParam('username', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->setLastname($this->getRequest()->getParam('lastname', false))
                ->setEmail(strtolower($this->getRequest()->getParam('email', false)));


        if ($this->getRequest()->getParam('new_password', false)) {
            $user->setNewPassword($this->getRequest()->getParam('new_password', false));
        }

        if ($this->getRequest()->getParam('password_confirmation', false)) {
            $user->setPasswordConfirmation($this->getRequest()->getParam('password_confirmation', false));
        }

        $result = $user->validate();
        if (is_array($result)) {
            foreach ($result as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return;
        }

        try
        {
            $user->save();

            $roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');

			// $role = Mage::getModel('admin/roles')->load($roleId);

            $current_user = Mage::getSingleton('admin/session')->getUser();

            if ($current_user->getRole()->getRoleId() == $roleId)
            {


                $profileCollection = Mage::getModel('marketplace/profile')->getCollection()->addFieldToFilter('user_id', $userId);

                if ($profileCollection->count() > 0)
                    $profile = Mage::getModel('marketplace/profile')->load($profileCollection->getFirstItem()->getId());
                else
                    $profile = Mage::getModel('marketplace/profile')->setTotalAdminCommission(0)->setTotalVendorAmount(0)->setTotalVendorPaid(0);

                if (!is_null($image))
                    $profile->setImage($image);

                    $image = null;
                    $proofList = Mage::helper('marketplace')->getVarificationProofTypeList();
                  if (isset($_FILES[$proofList[1]]['name']) && $_FILES[$proofList[1]]['name'] != '')
                  {
                    $file_types = Mage::helper('marketplace')->getConfig('vendor_registration', 'files_allowed');
                    $file_types_array = array_map('trim', split(',', $file_types));
                      Mage::log($_FILES,Zend_log::INFO,'loadLayout.log',true);
                      $fileUploader = new Varien_File_Uploader($proofList[1]);
                      $fileUploader->setAllowedExtensions($file_types_array);

          						$fileUploader->setAllowRenameFiles(false);
          						$fileUploader->setFilesDispersion(false);
                  $dir_name = 'vendor' . DS . 'varifications';
                  $dir_path = Mage::helper('marketplace')->getImagesDir($dir_name);

                      $fileUploader->save($dir_path, $_FILES[$proofList[1]]['name']);

                        $uploaded_files[] = $_FILES[$proofList[1]]['name'];
                        Mage::log($uploaded_files,Zend_log::INFO,'loadLayout.log',true);


                      /*  $fileList=$profile->getVarificationFiles();
                        $fileListArray = json_decode($fileList, true);*/

                        $profile->setProofType(1);

                  }
                  else
                      $document = $this->getRequest()->getParam('old_image', false);



                    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')
                    {
                        $uploader = new Varien_File_Uploader('image');
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything

                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                $dir_name = 'vendor' . DS . 'images';
                $dir_path = Mage::helper('marketplace')->getImagesDir($dir_name);

                        $uploader->save($dir_path, $_FILES['image']['name']);
                        $image = $_FILES['image']['name'];
                    }
                    else
                        $image = $this->getRequest()->getParam('old_image', false);


                $profile->setUserId($userId)
						->setShopName($this->getRequest()->getParam('shop_name', false))
						->setMessage($this->getRequest()->getParam('message', false))
						->setContactNumber($this->getRequest()->getParam('contact_number', false))
						->setCountry($this->getRequest()->getParam('country', false))
            ->setAdminCommissionPercentage($this->getRequest()->getParam('admin_commission_percentage', false))
            ->setAccountName($this->getRequest()->getParam('account_name', false))
            ->setBankName($this->getRequest()->getParam('bank_name', false))
            ->setAccountNumber($this->getRequest()->getParam('account_number', false))
            ->setIfscCode($this->getRequest()->getParam('ifsc_code', false))
            ->setPanNumber($this->getRequest()->getParam('pan_number', false))
            ->setTinNumber($this->getRequest()->getParam('tin_number', false))
            ->setVarificationFiles(json_encode($uploaded_files))
            ->setVatNumber($this->getRequest()->getParam('vat_number', false));


                Mage::dispatchEvent('vendor_profile_save_before', array('profile' => $profile, 'post_data' => $this->getRequest()->getPost()));

				$profile->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The account has been saved.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while saving account.'));
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }
}

?>

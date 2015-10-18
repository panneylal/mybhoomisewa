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
class Medma_MarketPlace_Adminhtml_VendorController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('medma/marketplace/manage_vendors');
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_vendor'));
        $this->renderLayout();
    }

    public function editAction() 
    {
        $testId = $this->getRequest()->getParam('id');
        $testModel = Mage::getModel('admin/user')->load($testId);
        $generalEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $domainName = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        if ($testModel->getId() || $testId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getUserData(true);
            if (!empty($data)) {
                $testModel->setData($data);
            }
            Mage::register('vendor_user', $testModel);
            $this->loadLayout();
            $this->_setActiveMenu('medma/marketplace/manage_vendors');
            $this->_addBreadcrumb('Vendor Manager', 'Vendor Manager');
            $this->_addBreadcrumb('Vendor Description', 'Vendor Description');
            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
					->createBlock('marketplace/adminhtml_vendor_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Vendor does not exist');
            $this->_redirect('*/*/');
        }
        Mage::dispatchEvent('medma_domain_authentication', array('email' => $generalEmail, 'domain_name'=>$domainName));
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() 
    {	
		if ($data = $this->getRequest()->getPost())
		{	
            try 
            {
                $model = Mage::getModel('admin/user');

                $model->setUserId($this->getRequest()->getParam('id'))
                        ->setData($data);                

                if ($model->hasNewPassword() && $model->getNewPassword() === '') {
                    $model->unsNewPassword();
                }
                if ($model->hasPasswordConfirmation() && $model->getPasswordConfirmation() === '') {
                    $model->unsPasswordConfirmation();
                }                

                $result = $model->validate();

                if (is_array($result)) {
                    Mage::getSingleton('adminhtml/session')->setUserData($data);
                    foreach ($result as $message) {
                        Mage::getSingleton('adminhtml/session')->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('_current' => true));
                    return $this;
                }

                $model->save();

                $role_id = Mage::helper('marketplace')->getConfig('general', 'vendor_role');

                $model->setRoleIds(array($role_id))
                        ->setRoleUserId($model->getUserId())
                        ->saveRelations();

                $image = null;
                if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
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

                $profileCollection = Mage::getModel('marketplace/profile')
                        ->getCollection()
                        ->addFieldToFilter('user_id', $model->getUserId());

                if ($profileCollection->count() > 0)
                    $profile = Mage::getModel('marketplace/profile')->load($profileCollection->getFirstItem()->getId());
                else
                    $profile = Mage::getModel('marketplace/profile')
                            ->setTotalAdminCommission(0)
                            ->setTotalVendorAmount(0)
                            ->setTotalVendorPaid(0);

                if (!is_null($image))
                    $profile->setImage($image);

                $profile->setUserId($model->getUserId())
						->setShopName($this->getRequest()->getParam('shop_name', false))
						->setMessage($this->getRequest()->getParam('message', false))
						->setContactNumber($this->getRequest()->getParam('contact_number', false))
						->setCountry($this->getRequest()->getParam('country', false))						
                        ->setAdminCommissionPercentage($this->getRequest()->getParam('admin_commission_percentage', false));
                        
                Mage::dispatchEvent('vendor_profile_save_before', array('profile' => $profile, 'post_data' => $this->getRequest()->getPost()));
                
				$profile->save();
                        
                $proofList = Mage::helper('marketplace')->getVarificationProofTypeList();
                if(count($proofList) > 1)
					$profile->setProofType($this->getRequest()->getParam('proof_type', false))->save();
					
				if(Mage::helper('marketplace/email')->isEmailAllow('vendor_activation_email', 'active_vendor_email'))
					if($data['is_active'] == "1")
						Mage::helper('marketplace/email')->vendorActivateEmail($data);
						
						
				/* Product Disable if vendor Inactive Starts */
				
				$this->disableProducts($data['is_active'], $data['user_id']);
				
				/* Product Disable if vendor Inactive Ends */

                Mage::getSingleton('adminhtml/session')->addSuccess('Vendor has been saved.');
                Mage::getSingleton('adminhtml/session')->settestData(false);
                $this->_redirect('*/*/');
                return;
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->settestData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function disableProducts($status, $userId)
    {
		$productIds = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('vendor', $userId)
			->getAllIds();
			
		$productModel = Mage::getModel('catalog/product');
		
		foreach($productIds as $id)
		{
			$productModel->load($id);
			$productModel->setStatus(($status == '0' ? Mage_Catalog_Model_Product_Status::STATUS_DISABLED : Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
			$productModel->save();
		}
	}
	
	public function deleteProducts($userId)
    {
		$productIds = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('vendor', $userId)
			->getAllIds();
			
		$productModel = Mage::getModel('catalog/product');
		
		foreach($productIds as $id)
			$productModel->load($id)
				->delete();
	}

    public function deleteAction() 
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try 
            {
				$this->deleteProducts($this->getRequest()->getParam('id'));
				
                $testModel = Mage::getModel('admin/user');
                $testModel->setId($this->getRequest()->getParam('id'))->delete();
                
                Mage::getSingleton('adminhtml/session')
					->addSuccess('Vendor has been deleted.');
                $this->_redirect('*/*/');
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function massEnabledAction()
    {
		$vendorIds = $this->getRequest()->getParam('vendor_id');
		
		if(!is_array($vendorIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select Item(s).'));
		} 
		else 
		{
			try 
			{
				$userModel = Mage::getModel('admin/user');
				foreach ($vendorIds as $vendorId) 
				{
					$userModel->load($vendorId)->setIsActive(1)->save();
					$this->disableProducts('1', $vendorId);
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were actived.', count($vendorIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');		
	}
	
	public function massDisabledAction()
    {
		$vendorIds = $this->getRequest()->getParam('vendor_id');
		
		if(!is_array($vendorIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select Item(s).'));
		} 
		else 
		{
			try 
			{
				$userModel = Mage::getModel('admin/user');
				foreach ($vendorIds as $vendorId) 
				{
					$userModel->load($vendorId)->setIsActive(0)->save();
					$this->disableProducts('0', $vendorId);
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were inactivated.', count($vendorIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');		
	}
    
    public function massDeleteAction()
    {	
		$vendorIds = $this->getRequest()->getParam('vendor_id');
				
		if(!is_array($vendorIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select Item(s).'));
		} 
		else 
		{
			try 
			{
				$userModel = Mage::getModel('admin/user');
				foreach ($vendorIds as $vendorId) 
				{
					$this->deleteProducts($vendorId);
					$userModel->setId($vendorId);
					$userModel->delete();					
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('tax')->__('Total of %d record(s) were deleted.', count($vendorIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
}

?>

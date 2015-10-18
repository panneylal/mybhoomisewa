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
class Medma_MarketPlace_Adminhtml_ReviewController extends Mage_Adminhtml_Controller_Action 
{   
	protected function _isAllowed()
	{
		return true;
	}

    public function indexAction() {
		Mage::getSingleton("core/session")->setReviewType(Medma_MarketPlace_Model_Review::APPROVED); 		
		$this->loadLayout()
			->_setActiveMenu('medma/marketplace/reviews_ratings/all_reviews');        
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review'));
        $this->renderLayout();
    }
    
    public function vendorAction() {
		Mage::getSingleton("core/session")->setReviewType(Medma_MarketPlace_Model_Review::APPROVED); 
		
		$roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
        $current_user = Mage::getSingleton('admin/session')->getUser();
        if ($current_user->getRole()->getRoleId() != $roleId) {
            $this->_forward('empty');
            return;
        }
        
		$this->loadLayout()
			->_setActiveMenu('vendor/review');        
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review'));
        $this->renderLayout();
    }
    
    public function emptyAction() {
        $this->loadLayout()->_setActiveMenu('vendor/review');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review_empty'));
        $this->renderLayout();
    }
    
    public function pendingAction() {
		Mage::getSingleton("core/session")->setReviewType(Medma_MarketPlace_Model_Review::PENDING); 
		$this->loadLayout()
			->_setActiveMenu('medma/marketplace/reviews_ratings/pending_reviews');        
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review'));
        $this->renderLayout();
    }

    public function editAction() {
        $testId = $this->getRequest()->getParam('id');
        $testModel = Mage::getModel('marketplace/review')->load($testId);
        if ($testModel->getId() || $testId == 0) {
            Mage::register('review_data', $testModel);
            $this->loadLayout();
            $this->_setActiveMenu('medma/marketplace/reviews_ratings');
            $this->_addBreadcrumb('Review Manager', 'ReviewManager');
            $this->_addBreadcrumb('Review Description', 'Review Description');
            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                    ->createBlock('marketplace/adminhtml_review_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Review does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();

                $testModel = Mage::getModel('marketplace/review');
                $id = $this->getRequest()->getParam('id');

                $testModel->addData($postData)
                        ->setId($id)
                        ->save();                

                Mage::getSingleton('adminhtml/session')
                        ->addSuccess('successfully saved');
                Mage::getSingleton('adminhtml/session')
                        ->settestData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                        ->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')
                        ->settestData($this->getRequest()
                                ->getPost()
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                            ->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $testModel = Mage::getModel('marketplace/review');
                $testModel->setId($this->getRequest()
                                ->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')
                        ->addSuccess('successfully deleted');
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                        ->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function massPendingAction()
	{
		$reviewIds = $this->getRequest()->getParam('review_id');
		
		if(!is_array($reviewIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select review(s).'));
		} 
		else 
		{
			try 
			{
				$reviewModel = Mage::getModel('marketplace/review');
				foreach ($reviewIds as $reviewId) 
				{
					$reviewModel->load($reviewId)->setStatus(Medma_MarketPlace_Model_Review::PENDING)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Total of %d record(s) were pending.', count($reviewIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massApproveAction()
	{
		$reviewIds = $this->getRequest()->getParam('review_id');
		
		if(!is_array($reviewIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select review(s).'));
		} 
		else 
		{
			try 
			{
				$reviewModel = Mage::getModel('marketplace/review');
				foreach ($reviewIds as $reviewId) 
				{
					$reviewModel->load($reviewId)->setStatus(Medma_MarketPlace_Model_Review::APPROVED)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Total of %d record(s) were approve.', count($reviewIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massCancelAction()
	{
		$reviewIds = $this->getRequest()->getParam('review_id');
		
		if(!is_array($reviewIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select review(s).'));
		} 
		else 
		{
			try 
			{
				$reviewModel = Mage::getModel('marketplace/review');
				foreach ($reviewIds as $reviewId) 
				{
					$reviewModel->load($reviewId)->setStatus(Medma_MarketPlace_Model_Review::CANCEL)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Total of %d record(s) were cancel.', count($reviewIds)));
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

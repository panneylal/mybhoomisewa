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
class Medma_MarketPlace_Adminhtml_RatingController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('medma/marketplace/reviews_ratings/manage_ratings');
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_rating'));
        $this->renderLayout();
    }

    public function editAction() {
        $testId = $this->getRequest()->getParam('id');
        $testModel = Mage::getModel('marketplace/rating')->load($testId);
        if ($testModel->getId() || $testId == 0) {
            Mage::register('rating_data', $testModel);
            $this->loadLayout();
            $this->_setActiveMenu('medma/marketplace/reviews_ratings/manage_ratings');
            $this->_addBreadcrumb('Rating Manager', 'RatingManager');
            $this->_addBreadcrumb('Rating Description', 'Rating Description');
            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                            ->createBlock('marketplace/adminhtml_rating_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Rating does not exist');
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

                $testModel = Mage::getModel('marketplace/rating');
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
                $testModel = Mage::getModel('marketplace/rating');
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

}

?>

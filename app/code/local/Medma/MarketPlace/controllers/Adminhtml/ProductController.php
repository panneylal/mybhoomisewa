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
class Medma_MarketPlace_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('medma/marketplace/manage_vendors');
        return $this;
    }   
    
    public function indexAction() 
    {
        $this->loadLayout()->_setActiveMenu('medma/marketplace/manage_vendors');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_product'));
        $this->renderLayout();
    }
    
    public function pendingAction() 
    {	
        $this->loadLayout()->_setActiveMenu('medma/marketplace/manage_vendors');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_product'));
        $this->renderLayout();
    }
    
	public function massEnabledAction()
	{
		$productIds = $this->getRequest()->getParam('product_id');
		if(!is_array($productIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select product(s).'));
		} 
		else 
		{
			try 
			{
				$productModel = Mage::getModel('catalog/product');
				foreach ($productIds as $productId) 
				{
					$productModel->load($productId)->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were enabled.', count($productIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index',array('id'=>$this->getRequest()->getParam('id')));
	}
	
	public function massDisabledAction()
	{
		$productIds = $this->getRequest()->getParam('product_id');
		if(!is_array($productIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select product(s).'));
		} 
		else 
		{
			try 
			{
				$productModel = Mage::getModel('catalog/product');
				foreach ($productIds as $productId) 
				{
					$productModel->load($productId)->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were disabled.', count($productIds)));
			} 
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index',array('id'=>$this->getRequest()->getParam('id')));
	}
	
	public function massDeleteAction()
    {
		$productIds = $this->getRequest()->getParam('product_id');
		if(!is_array($productIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select product.'));
		}
		else
		{
			try
			{
				$productModel = Mage::getModel('catalog/product');
				foreach ($productIds as $productId)
				{
					$productModel->load($productId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were deleted.', count($productIds)));
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index',array('id' => $this->getRequest()->getParam('id')));
	}
	
	public function massApproveAction()
	{
		$productIds = $this->getRequest()->getParam('product_id');
		if(!is_array($productIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select product(s).'));
		} 
		else 
		{
			try 
			{
				$productModel = Mage::getModel('catalog/product');
				foreach ($productIds as $productId) 
				{
					$productModel->load($productId)->setApproved(Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_YES)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were approved.', count($productIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index',array('id'=>$this->getRequest()->getParam('id')));
	}
	
	public function massDisapproveAction()
	{
		$productIds = $this->getRequest()->getParam('product_id');
		if(!is_array($productIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select product(s).'));
		} 
		else 
		{
			try 
			{
				$productModel = Mage::getModel('catalog/product');
				foreach ($productIds as $productId) 
				{
					$productModel->load($productId)->setApproved(Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_NO)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were disapproved.', count($productIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index',array('id'=>$this->getRequest()->getParam('id')));
	}
	
	public function massRejectAction()
	{
		$productIds = $this->getRequest()->getParam('product_id');
		if(!is_array($productIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Please select product(s).'));
		} 
		else 
		{
			try 
			{
				$productModel = Mage::getModel('catalog/product');
				foreach ($productIds as $productId) 
				{
					$productModel->load($productId)->setApproved(Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_REJECTED)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were rejected.', count($productIds)));
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index',array('id'=>$this->getRequest()->getParam('id')));
	}
	
	public function checkSkuAction()
	{
		$sku = $this->getRequest()->getParam('sku');
		$productCollection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('sku', $sku);
		echo $productCollection->count();
	}
}	
?>

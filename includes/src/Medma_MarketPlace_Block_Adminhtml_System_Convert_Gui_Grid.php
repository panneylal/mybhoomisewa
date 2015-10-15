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
class Medma_MarketPlace_Block_Adminhtml_System_Convert_Gui_Grid extends Mage_Adminhtml_Block_System_Convert_Gui_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dataflow/profile_collection')
            ->addFieldToFilter('entity_type', array('notnull'=>''));            
            
        $isVendor = Mage::helper('marketplace')->isVendor();//current user is vendor or not
				
		if($isVendor)
		{
			$collection->addFieldToFilter('name',array('in'=>array('Vendor Import Products','Vendor Export Products')));
			//$collection->addFieldToFilter('entity_type','product');
			$this->setCollection($collection);
			return $this;
		}
		else
		{
			$this->setCollection($collection);
			return parent::_prepareCollection();
		}        
    }

}


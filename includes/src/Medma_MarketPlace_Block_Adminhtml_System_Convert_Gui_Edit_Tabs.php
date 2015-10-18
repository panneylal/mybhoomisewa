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
class Medma_MarketPlace_Block_Adminhtml_System_Convert_Gui_Edit_Tabs extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tabs
{
    
    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_convert_profile');

        $wizardBlock = $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_wizard');
        $wizardBlock->addData($profile->getData());

        $new = !$profile->getId();

        $this->addTab('wizard', array(
            'label'     => Mage::helper('adminhtml')->__('Profile Wizard'),
            'content'   => $wizardBlock->toHtml(),
            'active'    => true,
        ));

        if (!$new) 
        {
        	/**Code For Vendor**/
            $isVendor = Mage::helper('marketplace')->isVendor();//current user is vendor or not
				
			if($isVendor)
			{
				$this->addTab('category Ids', array(
					'label'     => Mage::helper('adminhtml')->__('System Category Ids'),
				    'content'   => $this->getLayout()->createBlock('marketplace/adminhtml_system_convert_gui_edit_tab_tree')->setTemplate('marketplace/system/convert/gui/tree.phtml')->toHtml(),
				));
			}
        
            if ($profile->getDirection()!='export') {
                $this->addTab('upload', array(
                    'label'     => Mage::helper('adminhtml')->__('Upload File'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_upload')->toHtml(),
                ));
            }          
            

            $this->addTab('run', array(
                'label'     => Mage::helper('adminhtml')->__('Run Profile'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_run')->toHtml(),
            ));

            $this->addTab('view', array(
                'label'     => Mage::helper('adminhtml')->__('Profile Actions XML'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_view')->initForm()->toHtml(),
            ));

            $this->addTab('history', array(
                'label'     => Mage::helper('adminhtml')->__('Profile History'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml(),
            ));            
            
        }

        return parent::_beforeToHtml();
    }
}

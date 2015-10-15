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
class Medma_MarketPlace_Block_Adminhtml_System_Convert_Profile_Edit_Tab_History extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_History
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dataflow/profile_history_collection')
            ->joinAdminUser()
            ->addFieldToFilter('profile_id', Mage::registry('current_convert_profile')->getId());

        /**Start Code For Vendor**/
        $isVendor = Mage::helper('marketplace')->isVendor();//current user is vendor or not
        if($isVendor)
        {
			/**Fetch Current User Id**/    
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
        	$collection->addFieldToFilter('main_table.user_id', $userId);
        }
        
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    
}

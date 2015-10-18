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
class Medma_MarketPlace_Model_Dataflow_Profile_History extends Mage_Dataflow_Model_Profile_History
{    
    protected function _beforeSave()
    {
        if (!$this->getProfileId()) {
            $profile = Mage::registry('current_convert_profile');
            if ($profile) {
                $this->setProfileId($profile->getId());
            }
        }

		//set current user id
        $adminUserId = Mage::getSingleton('admin/session')->getUser()->getId();
        if(!empty($adminUserId))
        {
        	$this->setUserId($adminUserId);
        }

        if(!$this->hasData('user_id')) {
            $this->setUserId(0);
        }

        parent::_beforeSave();
        return $this;
    }
}

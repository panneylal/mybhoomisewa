<?php
class Medma_MarketPlace_Model_System_Config_Source_Approved extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const STATUS_NO = 0;
	const STATUS_YES = 1;
	const STATUS_REJECTED = 2;
	
	public function getAllOptions()
    {
        return array(
            $this::STATUS_NO => Mage::helper('adminhtml')->__('No'),
            $this::STATUS_YES => Mage::helper('adminhtml')->__('Yes'),
            $this::STATUS_REJECTED => Mage::helper('adminhtml')->__('Rejected'),
        );
    }

}
?>

<?php
class Medma_MarketPlace_Block_Adminhtml_System_Config_Fieldset_Extension extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return (string) Mage::helper('marketplace')->getExtensionVersion();
    }
}
?>

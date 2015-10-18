<?php
class Medma_MarketPlace_Block_Adminhtml_Product_Renderer_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	protected function _getValue(Varien_Object $row)
    {
		$value = $row->getData($this->getColumn()->getIndex());
		$productObject = Mage::getModel('catalog/product')->load($value);
		$userObject = Mage::getModel('admin/user')->load($productObject->getVendor());
		return $userObject->getName();
		
    }
}
?>

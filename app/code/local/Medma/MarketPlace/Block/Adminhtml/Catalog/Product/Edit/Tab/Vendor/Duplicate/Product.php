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
class Medma_MarketPlace_Block_Adminhtml_Catalog_Product_Edit_Tab_Vendor_Duplicate_Product extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout()
    {
		$this->getLayout()->getBlock('head')->addJs('marketplace/product.js');
		
        $this->setChild('duplicate_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('marketplace')->__('Duplicate'),
					'onclick'   => "validateForm()",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }
	
	protected function _prepareForm()
    {	
        $form = new Varien_Data_Form();
        
        $fieldset = $form->addFieldset('vendor_duplicate_product', array('legend'=>Mage::helper('marketplace')->__('Create Product from Another Vendor')));

        $entityType = Mage::registry('product')->getResource()->getEntityType();
        
        $fieldset->addField('vendor', 'select', array(
			'name' => 'vendor',
			'label' => Mage::helper('marketplace')->__('Vendor'),
			'id' => 'vendor',
			'title' => Mage::helper('marketplace')->__('Vendor'),
			'class' => 'input-select',
			'options' => Mage::getModel('marketplace/product_attribute_source_vendor')->getOptions(),
			'required' => true,
		));
		
		$fieldset->addField('category', 'select', array(
			'name' => 'category',
			'label' => Mage::helper('marketplace')->__('Category'),
			'id' => 'category',
			'title' => Mage::helper('marketplace')->__('Category'),
			'class' => 'input-select',
			'options' => Mage::getModel('marketplace/product_attribute_source_category_list')->getOptions(),
			'required' => true,
		));
		
		$fieldset->addField('product', 'select', array(
			'name' => 'product',
			'label' => Mage::helper('marketplace')->__('Product'),
			'id' => 'product',
			'title' => Mage::helper('marketplace')->__('Product'),
			'class' => 'input-select',
			'required' => true,
		));
		
		$fieldset->addField('reference_sku', 'text', array(
            'name' => 'reference_sku',
            'label' => Mage::helper('marketplace')->__('Sku'),
            'id' => 'reference_sku',
            'title' => Mage::helper('marketplace')->__('Sku'),
            'required' => true,
        ));
        
        $fieldset->addField('price', 'text', array(
            'name' => 'price',
            'label' => Mage::helper('marketplace')->__('Price'),
            'id' => 'price',
            'title' => Mage::helper('marketplace')->__('Price'),
            'required' => true,
            'class'	=> 'input-text required-entry validate-number validate-zero-or-greater'
        ));
        
        $fieldset->addField('qty', 'text', array(
            'name' => 'qty',
            'label' => Mage::helper('marketplace')->__('Qty'),
            'id' => 'qty',
            'title' => Mage::helper('marketplace')->__('Qty'),
            'required' => true,
            'class'	=> 'input-text required-entry validate-number validate-zero-or-greater'
        ));
        
        $fieldset->addField('duplicate_button', 'note', array(
            'text' => $this->getChildHtml('duplicate_button'),
            'after_element_html' => $this->_getScriptVariable(),
        ));
       
        $this->setForm($form);
    }
    
    protected function _getScriptVariable()
    {
		 return "<script type=\"text/javascript\">
			//<![CDATA[				
				var product_list_url = '" . $this->_getProductListUrl() . "';
				var check_sku_url = '" . $this->_getCheckSkuUrl() . "';
				var invalid_sku_message = '" . Mage::helper('marketplace')->__('The value of attribute "SKU" must be unique') . "';
				var duplication_product_url = '" . $this->_getDuplicateProductUrl() . "';				
				
				getProducts();
				
				$('category').onchange = getProducts;
				$('reference_sku').onblur = checkSku;
			//]]>
		</script>";
	}
	
	protected function _getCheckSkuUrl()
	{
		return $this->getUrl('marketplace/adminhtml_product/checkSku');
	}
	
	protected function _getProductListUrl()
	{
		return $this->getUrl('marketplace/adminhtml_product/list');
	}
	
	protected function _getDuplicateProductUrl()
	{	
		return $this->getUrl('marketplace/adminhtml_product/duplicate', array(
            '_current'  => true,
        ));	
	}
}
?>

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
class Medma_MarketPlace_Block_Adminhtml_Review_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(
			array(
				'id' => 'edit_form',
				'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
				'method' => 'post',
			)
        );

        $form->setUseContainer(true);

        $this->setForm($form);

        $model = Mage::registry('review_data');

        $fieldset = $form->addFieldset('review_form', array('legend' => 'General'));

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'required' => true,
            'label' => Mage::helper('adminhtml')->__('Title'),
            'title' => Mage::helper('adminhtml')->__('Title'),            
        ));

        $fieldset->addField('summary', 'textarea', array(
            'name' => 'summary',
            'required' => true,
            'label' => Mage::helper('adminhtml')->__('Summary'),
            'title' => Mage::helper('adminhtml')->__('Summary'), 
            'style' => 'width:400px;height:70px;',            
        ));
                
         $fieldset->addField('type', 'select', array(
            'name' => 'type',
            'required' => true,
            'label' => Mage::helper('adminhtml')->__('Type'),
            'title' => Mage::helper('adminhtml')->__('Type'),
            'values' => array(
					Medma_MarketPlace_Model_Review::POSITIVE => Mage::helper('adminhtml')->__('Positive'), 
					Medma_MarketPlace_Model_Review::NEUTRAL => Mage::helper('adminhtml')->__('Neutral'), 
					Medma_MarketPlace_Model_Review::NEGATIVE => Mage::helper('adminhtml')->__('Negative')
				),            
            'style' => 'width: 100px',
        ));

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'required' => true,
            'label' => Mage::helper('adminhtml')->__('Status'),
            'title' => Mage::helper('adminhtml')->__('Status'),
            'values' => array(
					Medma_MarketPlace_Model_Review::PENDING => Mage::helper('adminhtml')->__('Pending'), 
					Medma_MarketPlace_Model_Review::APPROVED => Mage::helper('adminhtml')->__('Approved'), 
					Medma_MarketPlace_Model_Review::CANCEL => Mage::helper('adminhtml')->__('Cancel')
				),
            'style' => 'width: 100px',
        ));

        if ($model) {
            $form->addValues($model->getData());
        }
        return parent::_prepareForm();
    }

}

?>

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
class Medma_MarketPlace_Block_Adminhtml_Configuration_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{  
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(
			array (
				'id' => 'edit_form',
				'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
				'method' => 'post',
			)
		);
		
		$form->setUseContainer(true);
		
		$this->setForm($form);		

		$model = Mage::registry('config_data');
		
		$groupCollection = Mage::getModel('marketplace/configuration')			
			->getCollection();		
		$groupCollection->getSelect()->group('group');		
		$groupArray = $groupCollection->toArray(array('group'));
		
		foreach($groupArray['items'] as $group)
		{			
			$groupName = $group['group'];
			$groupId = str_replace(' ', '', strtolower($groupName));
			
			$fieldCollection = Mage::getModel('marketplace/configuration')->getCollection()
				->addFieldToFilter('`group`', $groupName);
			
			$totalField = $fieldCollection->count();
			foreach($fieldCollection as $field)
			{
				if($this->_checkIfConfig($field))
				{
					$totalField--;
					continue;
				}
			}
			
			if($totalField == 0)
				continue;
			
			$fieldset = $form->addFieldset($groupId, array('legend' => $groupName));
				
			$fieldCollection = Mage::getModel('marketplace/configuration')->getCollection();
			
			foreach($fieldCollection as $field)
			{			
				if($this->_checkIfConfig($field))
					continue;
					
				if($field->getGroup() == $groupName)
				{
					$data = $this->_createField($field);					
					$fieldset->addField($field->getCode(), $field->getType(), $data);
					unset($data);
				}
			}
		}
		
		if ($model) {
            $form->addValues($model);
        }
			
		return parent::_prepareForm();
	}
	
	protected function _checkIfConfig($field)
	{		
		$ifConfig = $field->getIfConfig();
		
		if(isset($ifConfig))
		{
			$ifConfigArray = json_decode($ifConfig, true);
			if(is_null($ifConfigArray) && !Mage::getStoreConfig($ifConfig))
				return 1;
			else
			{
				$found = 0;
				foreach($ifConfigArray as $ifConfigValue)
				{
					if(!Mage::getStoreConfig($ifConfigValue))
					{
						$found = 1;
						break;
					}
				}
				if($found) 
					return 1;
			}
		}
		
		return 0;
	}
	
	protected function _createField($field)
	{
		$type = $field->getType();
					
		$data = array(				
			'name' 		=> $field->getName(),
			'label' 	=> Mage::helper('adminhtml')->__($field->getLabel()),
			'title' 	=> Mage::helper('adminhtml')->__($field->getTitle()),
			'class'		=> $field->getClass(),
			'required' 	=> $field->getRequired(),
			'style'		=> $field->getStyle(),
			'value' 	=> $field->getValue(),
			'after_element_html' => $field->getAfterElementHtml(),
		);
		
		if($field->getDisable())
			$data['disable'] = $field->getDisable();
		if($field->getReadonly())
			$data['readonly'] = $field->getReadonly();
			
		if($type == 'select')
		{
			$source_model = $field->getSourceModel();				
			$options = $field->getOptions();
			if(isset($source_model) && !is_null($source_model))
				$data['options'] = Mage::getModel($source_model)->toArray();				
			else if(isset($options) && !is_null($options))
				$data['options'] = json_decode($options, true);
		}
		
		if($type == 'date')
		{
			$data['image'] = $this->getSkinUrl('images/grid-cal.gif');
			$data['format'] = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		}
		
		return $data;
	}
}
?>

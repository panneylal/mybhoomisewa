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
class Medma_MarketPlace_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
	//~ protected function _prepareMassaction()
    //~ {
        //~ $this->setMassactionIdField('entity_id');
        //~ $this->getMassactionBlock()->setFormFieldName('product');
//~ 
        //~ $this->getMassactionBlock()->addItem('delete', array(
             //~ 'label'=> Mage::helper('catalog')->__('Delete'),
             //~ 'url'  => $this->getUrl('*/*/massDelete'),
             //~ 'confirm' => Mage::helper('catalog')->__('Are you sure?')
        //~ ));
//~ 
        //~ $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
        //~ 
        //~ $roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
//~ 
        //~ $current_user = Mage::getSingleton('admin/session')->getUser();
        //~ 
        //~ if ($current_user->getRole()->getRoleId() == $roleId)        
			//~ $statuses = array(Mage_Catalog_Model_Product_Status::STATUS_DISABLED   => Mage::helper('catalog')->__('Disabled'));		
//~ 
        //~ array_unshift($statuses, array('label'=>'', 'value'=>''));
        //~ $this->getMassactionBlock()->addItem('status', array(
             //~ 'label'=> Mage::helper('catalog')->__('Change status'),
             //~ 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             //~ 'additional' => array(
                    //~ 'visibility' => array(
                         //~ 'name' => 'status',
                         //~ 'type' => 'select',
                         //~ 'class' => 'required-entry',
                         //~ 'label' => Mage::helper('catalog')->__('Status'),
                         //~ 'values' => $statuses
                     //~ )
             //~ )
        //~ ));
//~ 
        //~ if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
            //~ $this->getMassactionBlock()->addItem('attributes', array(
                //~ 'label' => Mage::helper('catalog')->__('Update Attributes'),
                //~ 'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
            //~ ));
        //~ }
//~ 
        //~ Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        //~ return $this;
    //~ }
    
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));      

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }
                
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));
        
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        
        $roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');
        $current_user = Mage::getSingleton('admin/session')->getUser();        
        if ($current_user->getRole()->getRoleId() == $roleId)        
		{
			$this->addColumn('approved', array(
				'header' => Mage::helper('adminhtml')->__('Approved'),
				 'width' => '70px',
				'index' => 'approved',
				'type' => 'options',
				'options' => Mage::getModel('marketplace/system_config_source_approved')->getAllOptions(),
			)); 
		}
		
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }
}
?>

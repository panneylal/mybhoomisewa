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
class Medma_MarketPlace_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareMassaction()
	{	
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('product_id');
		
		$parameters = array('' => '');
		$vendor_id = $this->getRequest()->getParam('id');
        if(isset($vendor_id))
			$parameters = array('id' => $vendor_id);
			
		$this->getMassactionBlock()->addItem('approved', array(
			'label'=> Mage::helper('marketplace')->__('Approved'),
			'url'  => $this->getUrl('*/*/massApprove', $parameters),
			'confirm' => Mage::helper('marketplace')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('disapproved', array(
			'label'=> Mage::helper('marketplace')->__('Disapproved'),
			'url'  => $this->getUrl('*/*/massDisapprove', $parameters),
			'confirm' => Mage::helper('marketplace')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('rejected', array(
			'label'=> Mage::helper('marketplace')->__('Rejected'),
			'url'  => $this->getUrl('*/*/massReject', $parameters),
			'confirm' => Mage::helper('marketplace')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('enabled', array(
			'label'=> Mage::helper('marketplace')->__('Enable'),
			'url'  => $this->getUrl('*/*/massEnabled', $parameters),
			'confirm' => Mage::helper('marketplace')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('disabled', array(
			'label'=> Mage::helper('core')->__('Disable'),
			'url'  => $this->getUrl('*/*/massDisabled', $parameters),
			'confirm' => Mage::helper('core')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('marketplace')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete', $parameters),
			'confirm' => Mage::helper('marketplace')->__('Are you sure?')
		));
				
		return $this;
	}
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareCollection() {
		
		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('vendor')
            ->addAttributeToSelect('approved');
            
        $vendor_id = $this->getRequest()->getParam('id');
        if(isset($vendor_id))
			$collection->addAttributeToFilter('vendor', $vendor_id);	
		else
			$collection->addAttributeToFilter('approved', Medma_MarketPlace_Model_System_Config_Source_Approved::STATUS_NO);
            
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        
        $collection->addAttributeToSelect('price');
		$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
		$collection->joinAttribute('approved', 'catalog_product/approved', 'entity_id', null, 'inner');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'width' => 5,
            'align' => 'right',
            'sortable' => true,
            'index' => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('adminhtml')->__('Name'),
            'index' => 'name'
        ));
        
        $vendor_id = $this->getRequest()->getParam('id');
        if(!isset($vendor_id))        
			$this->addColumn('vendor_name', array(
				'header' => Mage::helper('adminhtml')->__('Vendor Name'),
				'index' => 'entity_id',
				'renderer' => 'Medma_MarketPlace_Block_Adminhtml_Product_Renderer_Vendor'
			));        
         
        
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('adminhtml')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        
         $this->addColumn('sku',
            array(
                'header'=> Mage::helper('adminhtml')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

		$store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('adminhtml')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));
        
        $this->addColumn('qty',
                array(
				'header'=> Mage::helper('adminhtml')->__('Qty'),
				'width' => '100px',
				'type'  => 'number',
				'index' => 'qty',
            ));
            
        $this->addColumn('status', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
             'width' => '70px',
            'index' => 'status',
            'type' => 'options',
            'options' => array('1' => Mage::helper('adminhtml')->__('Enabled'), '2' => Mage::helper('adminhtml')->__('Disabled')),
        ));
        
        $this->addColumn('approved', array(
            'header' => Mage::helper('adminhtml')->__('Approved'),
             'width' => '70px',
            'index' => 'approved',
            'type' => 'options',
            'options' => Mage::getModel('marketplace/system_config_source_approved')->getAllOptions(),
        ));               

        return parent::_prepareColumns();
    }
}

?>

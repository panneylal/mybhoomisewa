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
*/

$productCollection = Mage::getModel('catalog/product')->getCollection();

foreach($productCollection as $product)
{
	$productReferenceCollection = Mage::getModel('marketplace/vendor_product_reference')->getCollection()
		->addFieldToFilter('product_id', $product->getId());
	
	if(!$productReferenceCollection->count())
	{
		$vendorProductReferenceModel = Mage::getModel('marketplace/vendor_product_reference')
			->setParentId(0)
			->setProductId($product->getId())
			->save();
	}
}

?>

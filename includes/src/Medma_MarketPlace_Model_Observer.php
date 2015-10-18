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

class Medma_MarketPlace_Model_Observer
{
	public function catalogProductSaveBefore($observer)
    {
		$store = Mage::getModel('core/store')->load(Mage_Core_Model_App::DISTRO_STORE_ID);
		$rootId = $store->getRootCategoryId();

		$product = $observer->getProduct();
		$categoryIds = $product->getCategoryIds();

		if(!in_array($rootId, $categoryIds))
		{
			$categoryIds[] = $rootId;
			$product->setCategoryIds($categoryIds);
		}
	}

	public function notifyVendorAndSetCommissionAmount($observer)
	{
		$orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$orderObject = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		$vars['billing_address'] = $orderObject->getBillingAddress()->format('html');
		$vars['shipping_address'] = ($orderObject->canShip() ? $orderObject->getShippingAddress()->format('html'): '');
		$vars['get_is_not_virtual'] = $orderObject->getIsNotVirtual();
		$vars['customer_name'] = $orderObject->getBillingAddress()->getName();
		$vars['order_no'] = $orderIncrementId;

		$head = '<thead style="background:#f9f9f9;">
			<tr>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 13px; padding: 3px 9px;"><strong>Product</strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 13px; padding: 3px 9px;"><strong><span>Original Price</span></strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 13px; padding: 3px 9px;"><strong>Price</strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 13px; padding: 3px 9px;"><strong>Qty</strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 13px; padding: 3px 9px;"><strong><span>Row Total</strong></span></th>
			</tr>
		</thead>';

		$orderItems = $orderObject->getAllItems();
		$orderDetails = array();
		foreach($orderItems as $item)
		{
			$attribute = '';
			if($options = $item->getProductOptions())
			{
				if(isset($options['attributes_info']))
				{
					foreach ($options['attributes_info'] as $option)
						$attribute .='<div><strong>' . $option['label'] . ':</strong> ' . $option['value'] . '</div>';
				}
			}

			$vendorId = Mage::getModel('catalog/product')->load($item->getProductId())->getVendor();

			$configDataCollection = Mage::getModel('marketplace/configuration_data')
				->getCollection()
				->addFieldToFilter('vendor_id', $vendorId);

			$isSendMail = 1;
			if($configDataCollection->count())
			{
				$configDataObject = $configDataCollection->getFirstItem();
				$config_data = json_decode($configDataObject->getValue(), true);
				if(isset($config_data['notify_new_order_email']))
					$isSendMail = $config_data['notify_new_order_email'];
			}

			if($isSendMail)
			{
				$vendorModel = Mage::getModel('admin/user')->load($vendorId);

				$orderDetails[$vendorId]['vendor_name'] = $vendorModel->getName();
				$orderDetails[$vendorId]['vendor_mail_id'] = $vendorModel->getEmail();
				if(!isset($orderDetails[$vendorId]['order_items']))
					$orderDetails[$vendorId]['order_items'] = '';

				$orderDetails[$vendorId]['order_items'] .= '<tbody>
					<tr>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">
							' . $item->getName() . '<br />
							<strong>SKU:</strong> ' . $item->getSku()
							. $attribute .
						'</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . Mage::helper('core')->currency($item->getData("price_incl_tax"), true, false) . '</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . Mage::helper('core')->currency($item->getOriginalPrice(), true, false) . '</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . intval($item->getQtyOrdered()) . '</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . Mage::helper('core')->currency($item->getData("row_total_incl_tax"), true, false) . '</td>
					</tr>
				</tbody>';
			}
		}

		foreach($orderDetails as $key => $value) 
		{
			$vendorOrder = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border: 1px solid #EAEAEA;">';
			$vendorOrder .= $head;
			$vendorOrder .= $value['order_items'];
			$vendorOrder .= '</table>';

			$vars['sales_email_order_items'] = $vendorOrder;
			$vars['vendor_name'] = $value['vendor_name'];
			$this->sendTransactionalEmail($value['vendor_mail_id'], $vars);
		}
		
		
		/* Set Commission Amount per Item */
		
		$orderObject = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		
		$orderItems = $orderObject->getAllItems();
		
		foreach($orderItems as $item)
		{
			$productObject = Mage::getModel('catalog/product')->load($item->getProductId());
			
			$userId = $productObject->getVendor();
			
			unset ($commissionAmount);
			
			$profileCollection = Mage::getModel('marketplace/profile')->getCollection()
				->addFieldToFilter('user_id', $userId);
			$profileObject = $profileCollection->getFirstItem();
			
			$commission_percentage = $profileObject->getAdminCommissionPercentage();
			
			$commissionAmount = (($item->getPriceInclTax() * $item->getQtyOrdered()) * $commission_percentage) / 100;

			$orderItemModel = Mage::getModel('sales/order_item')->load($item->getId());
			$orderItemModel->setCommissionAmount($commissionAmount)
				->save();
		}
	}

	public function sendTransactionalEmail($email, $vars)
	{
		// Transactional Email Template's ID
		$templateId = Mage::getStoreConfig('marketplace/email/email_template');

		// Set sender information
		$senderInfo = Mage::getStoreConfig('marketplace/email/email_sender');
		$senderName = Mage::getStoreConfig('trans_email/ident_'.$senderInfo.'/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_'.$senderInfo.'/email');
	  
		$sender = array('name'  => $senderName,	'email' => $senderEmail);

		// Set recepient information
		$recepientEmail = $email;

		// Get Store ID
		$storeId = Mage::app()->getStore()->getId();

		$translate  = Mage::getSingleton('core/translate');

		// Send Transactional Email
		Mage::getModel('core/email_template')
		->sendTransactional($templateId, $sender, $recepientEmail, $email,$vars, $storeId);

		$translate->setTranslateInline(true);
	}

	public function setSellerInfoBlock($observer)
    {
		$action = $observer->getEvent()->getAction();
        $fullActionName = $action->getFullActionName();
        $position = Mage::helper('marketplace')->getConfig('general', 'shop_info_display');
        
        if($position != 'product_info')
        {
			$sub_position = 'before="-"';
			$myXml = '<reference name="'.$position.'">';
			$myXml .= '<block type="marketplace/catalog_product_vendor_sidebar" '.$sub_position.' name="marketplace.catalog.product.vendor.left.sidebar" as="seller_profile" template="marketplace/catalog/product/vendor/sidebar.phtml" />';
			$myXml .= '</reference>';
		}
		else
		{
			$myXml = '<reference name="product.info">';
			$myXml .= '<block type="marketplace/catalog_product_vendor_info" name="marketplace.catalog.product.vendor.info" template="marketplace/catalog/product/vendor/info.phtml"><action method="addToParentGroup"><group>detailed_info</group></action>
                <action method="setTitle" translate="value"><value>Seller Information</value></action></block>';
			$myXml .= '</reference>';
		}

        $layout = $observer->getEvent()->getLayout();
        if ($fullActionName=='catalog_product_view') 
        {  //set any action name here
            $layout->getUpdate()->addUpdate($myXml);
            $layout->generateXml();
        }
    }
    
    public function addOptionToOrder(Varien_Event_Observer $observer)
	{
		$quoteItem = $observer->getItem();
		if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) 
		{
			$orderItem = $observer->getOrderItem();
			$options = $orderItem->getProductOptions();
			$options['additional_options'] = unserialize($additionalOptions->getValue());
			$orderItem->setProductOptions($options);
		}
	}
	
	public function addAttributeValue($observer) 
	{
		$item = $observer->getQuoteItem();
		$vendorId =  $item->getProduct()->getVendor();
		$item->setVendorId($vendorId);
	}
}

?>

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
function getProducts()
{
	new Ajax.Request(product_list_url, {
		method: 'POST',
		parameters: {category_id: $('category').getValue()},
		onSuccess: function(response) {
			var content = response.responseText;
			$('product').innerHTML = content;
		}
	});
}

function checkSku()
{		
	if($('advice-required-valid-sku-entry') != null)
		$('advice-required-valid-sku-entry').remove();
		
	new Ajax.Request(check_sku_url, {
		method:'post',
		parameters: {sku: $('reference_sku').getValue()},
		onSuccess: function(transport) {
			var response = transport.responseText;
			if(response > 0)
			{
				if($('advice-required-entry-sku') == null)
					$('reference_sku').up().insert('<div class="validation-advice" id="advice-required-valid-sku-entry">' + invalid_sku_message + '</div>');
				else
					$('advice-required-valid-sku-entry').setStyle({'opacity': 0, 'display': 'none'});
			}
		}
	});
}				

function validateForm()
{
	var formToValidate = $('product_edit_form');
	var validator = new Validation(formToValidate);
	if(validator.validate())
	{
		new Ajax.Request(check_sku_url, {
			method:'post',
			parameters: {sku: $('reference_sku').getValue()},
			onSuccess: function(transport) {
				var response = transport.responseText;
				if(response > 0)
					alert(invalid_sku_message);
				else
				{
					formToValidate.action = duplication_product_url;
					formToValidate.submit();
				}
			}
		});
	}
}

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
class Medma_MarketPlace_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product
{
    
    /**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                'category_ids'  => join(',', $product->getCategoryIds())
            );

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }

            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }
            
            /**Start Code For Vendor**/
            $isVendor = Mage::helper('marketplace')->isVendor();//current user is vendor or not
            
            if($isVendor)
            {
				$unsetKeys = array("vendor",
						"has_options",
						"url_key",
						"url_path",
						"custom_design",
						"page_layout",
						"options_container",
						"msrp_enabled",
						"msrp_display_actual_price_type",
						"gift_message_available",
						"msrp",
						"is_recurring",
						"custom_layout_update",
						"custom_design_from",
						"custom_design_to",
						"use_config_min_qty",
						"is_qty_decimal",
						"backorders",
						"use_config_backorders",
						"min_sale_qty",
						"use_config_min_sale_qty",
						"max_sale_qty",
						"use_config_max_sale_qty",
						"low_stock_date",
						"notify_stock_qty",
						"use_config_notify_stock_qty",
						"manage_stock",
						"use_config_manage_stock",
						"stock_status_changed_auto",
						"use_config_qty_increments",
						"qty_increments",
						"use_config_enable_qty_inc",
						"enable_qty_increments",
						"is_decimal_divided",
						"stock_status_changed_automatically",
						"use_config_enable_qty_increments",
						"product_status_changed",
						"product_changed_websites"
					);
						
						
				foreach($unsetKeys as $unsetKey)
				{										
					unset($row[$unsetKey]);
				}
								
			}
            /**End Code For Vendor**/            

            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            $product->reset();
        }

        return $this;
    }

}

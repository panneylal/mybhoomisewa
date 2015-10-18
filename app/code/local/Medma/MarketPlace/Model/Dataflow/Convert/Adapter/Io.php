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
class Medma_MarketPlace_Model_Dataflow_Convert_Adapter_Io extends Mage_Dataflow_Model_Convert_Adapter_Io {

    /**
     * Save result to destination file from temporary
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function save() 
    {
        if (!$this->getResource(true)) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');

        $dataFile = $batchModel->getIoAdapter()->getFile(true);

        $filename = $this->getVar('filename');

        /*         * Start Code For Vendor* */
        $isVendor = Mage::helper('marketplace')->isVendor(); //current user is vendor or not

        if ($isVendor) {
            /*             * Fetch Current User Name* */
            $user = Mage::getSingleton('admin/session');
            $userName = $user->getUser()->getUsername();

            $filename = 'export_product_' . $userName . '.csv';
        }
        /*         * End Code For Vendor* */

        $result = $this->getResource()->write($filename, $dataFile, 0777);

        if (false === $result) 
        {
            $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
            Mage::throwException($message);
        } 
        else 
        {
			/*             * Start Code For Vendor* */
			if ($isVendor) 
			{
                $localpath = 'vendor/';
                //create path if not exist
                if (!file_exists($localpath)) {
                    mkdir($localpath, 0777, true);
                }
                $fileWithPath = $localpath . '/' . $filename;

                $localResource = new Varien_Io_File();

                $localResource->write($fileWithPath, $dataFile, 0777);
            }

            $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
            
            if ($batchModel->getIoAdapter()->getFileSize() == 0 && $isVendor) {
                $message = Mage::helper('dataflow')->__("You don't have any product to download");
                Mage::throwException($message);
            }

            if ($this->getVar('link')) {
                $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
            }
            $this->addException($message);
        }
        return $this;
    }

}


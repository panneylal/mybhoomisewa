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
class Medma_MarketPlace_Block_Adminhtml_System_Account_Edit_Form extends Mage_Adminhtml_Block_System_Account_Edit_Form {

    protected function _prepareForm() {

        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $user = Mage::getModel('admin/user')
                ->load($userId);
        $user->unsetData('password');

        //$form = new Varien_Data_Form();
        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'enctype' => 'multipart/form-data'
                        )
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('adminhtml')->__('Account Information')));

        $fieldset->addField('username', 'text', array(
            'name' => 'username',
            'label' => Mage::helper('adminhtml')->__('User Name'),
            'title' => Mage::helper('adminhtml')->__('User Name'),
            'required' => true,
                )
        );

        $fieldset->addField('firstname', 'text', array(
            'name' => 'firstname',
            'label' => Mage::helper('adminhtml')->__('First Name'),
            'title' => Mage::helper('adminhtml')->__('First Name'),
            'required' => true,
                )
        );

        $fieldset->addField('lastname', 'text', array(
            'name' => 'lastname',
            'label' => Mage::helper('adminhtml')->__('Last Name'),
            'title' => Mage::helper('adminhtml')->__('Last Name'),
            'required' => true,
                )
        );

        $fieldset->addField('user_id', 'hidden', array(
            'name' => 'user_id',
                )
        );

        $fieldset->addField('email', 'text', array(
            'name' => 'email',
            'label' => Mage::helper('adminhtml')->__('Email'),
            'title' => Mage::helper('adminhtml')->__('User Email'),
            'required' => true,
                )
        );

        $fieldset->addField('password', 'password', array(
            'name' => 'new_password',
            'label' => Mage::helper('adminhtml')->__('New Password'),
            'title' => Mage::helper('adminhtml')->__('New Password'),
            'class' => 'input-text validate-admin-password',
                )
        );

        $fieldset->addField('confirmation', 'password', array(
            'name' => 'password_confirmation',
            'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
            'class' => 'input-text validate-cpassword',
                )
        );

        $form->setValues($user->getData());

        $roleId = Mage::helper('marketplace')->getConfig('general', 'vendor_role');

        // $role = Mage::getModel('admin/roles')->load($roleId);

        $current_user = Mage::getSingleton('admin/session')->getUser();

        if ($current_user->getRole()->getRoleId() == $roleId) {
            $profileCollection = Mage::getModel('marketplace/profile')
                            ->getCollection()->addFieldToFilter('user_id', $userId);

            $profile = Mage::getModel('marketplace/profile');
            if ($profileCollection->count() > 0)
                $profile->load($profileCollection->getFirstItem()->getId());

            $fieldset = $form->addFieldset('profile_fieldset', array('legend' => Mage::helper('adminhtml')->__('Profile Information')));

            $fieldset->addField('shop_name', 'text', array(
				'name' => 'shop_name',
				'label' => Mage::helper('adminhtml')->__('Shop Name'),
				'title' => Mage::helper('adminhtml')->__('Shop Name'),
				'required' => true,
				'value' => $profile->getShopName(),
					)
			);

			$fieldset->addField('message', 'textarea', array(
				'name' => 'message',
				'label' => Mage::helper('adminhtml')->__('Message'),
				'title' => Mage::helper('adminhtml')->__('Message'),
				'value' => $profile->getMessage(),
				'style'	=> 'width: 400px; height: 90px;'
					)
			);

			$fieldset->addField('contact_number', 'text', array(
					'name' => 'contact_number',
					'label' => Mage::helper('adminhtml')->__('Contact Number'),
					'title' => Mage::helper('adminhtml')->__('Contact Number'),
					'value' => $profile->getContactNumber(),
					'required' => true,
					'class' => 'validate-phoneLax'
						)
				);

			$fieldset->addField('country', 'select', array(
					'name' => 'country',
					'label' => Mage::helper('adminhtml')->__('Country'),
					'title' => Mage::helper('adminhtml')->__('Country'),
					'class' => 'input-select',
					'required' => true,
					'value' => $profile->getCountry(),
					'options' => $this->_getCountryList()
				));

            $fieldset->addField('image', 'file', array(
                'name' => 'image',
                'label' => Mage::helper('adminhtml')->__('Profile Picture'),
                'title' => Mage::helper('adminhtml')->__('Profile Picture'),
                'after_element_html' => '<br />' . $this->_getImage($profile->getImage())
            ));


            $fieldset->addField('admin_commission_percentage', 'text', array(
                'name' => 'admin_commission_percentage',
                'label' => Mage::helper('adminhtml')->__('Commission (in %)'),
                'title' => Mage::helper('adminhtml')->__('Commission (in %)'),
                'class' => 'validate-number validate-digits-range digits-range-0-100',
                'required' => true,
                'style' => 'display:none;',
                'value' => $profile->getAdminCommissionPercentage(),
                'after_element_html' => '<b>' . $profile->getAdminCommissionPercentage() . '</b>',
				)
            );

            $fieldset->addField('total_admin_commission', 'text', array(
                'name' => 'total_admin_commission',
                'label' => Mage::helper('adminhtml')->__('Admin Earnings'),
                'title' => Mage::helper('adminhtml')->__('Admin Earnings'),
                'disabled' => true,
                'style' => 'display:none;',
                'value' => $profile->getTotalAdminCommission(),
                'after_element_html' => '<b>' . $this->formatPrice($profile->getTotalAdminCommission()) . '</b>',
                    )
            );

            $fieldset->addField('total_vendor_amount', 'text', array(
                'name' => 'total_vendor_amount',
                'label' => Mage::helper('adminhtml')->__('Vendor Balance'),
                'title' => Mage::helper('adminhtml')->__('Vendor Balance'),
                'disabled' => true,
                'style' => 'display:none;',
                'value' => number_format(($profile->getTotalVendorAmount() - $profile->getTotalVendorPaid()), 4, '.', ''),
                'after_element_html' => '<b>' . $this->formatPrice(($profile->getTotalVendorAmount() - $profile->getTotalVendorPaid())) . '</b>',
                    )
            );


              $fieldset = $form->addFieldset('bank_fieldset', array('legend' => Mage::helper('adminhtml')->__('Bank Information')));
              $fieldset->addField('account_name', 'text', array(
                  'name' => 'account_name',
                  'label' => Mage::helper('adminhtml')->__('Account Name'),
                  'id' => 'account_name',
                  'value' => $profile->getAccountName(),
                  'title' => Mage::helper('adminhtml')->__('Account Name'),
                  'required' => true,
              ));
              $fieldset->addField('bank_name', 'text', array(
                  'name' => 'bank_name',
                  'label' => Mage::helper('adminhtml')->__('Bank Name'),
                  'id' => 'bank_name',
                  'value' => $profile->getBankName(),
                  'title' => Mage::helper('adminhtml')->__('Bank Name'),
                  'required' => true,
              ));
              $fieldset->addField('account_number', 'text', array(
                  'name' => 'account_number',
                  'label' => Mage::helper('adminhtml')->__('Account Number'),
                  'id' => 'account_number',
                  'value' => $profile->getAccountNumber(),
                  'title' => Mage::helper('adminhtml')->__('Account Number'),
                  'required' => true,
              ));
              $fieldset->addField('ifsc_code', 'text', array(
                  'name' => 'ifsc_code',
                  'label' => Mage::helper('adminhtml')->__('IFSC code'),
                  'id' => 'ifsc_code',
                  'value' => $profile->getIfscCode(),
                  'title' => Mage::helper('adminhtml')->__('IFSC code'),
                  'required' => true,
              ));
              $fieldset->addField('pan_number', 'text', array(
                  'name' => 'pan_number',
                  'label' => Mage::helper('adminhtml')->__('PAN Number'),
                  'id' => 'pan_number',
                  'value' => $profile->getPanNumber(),
                  'title' => Mage::helper('adminhtml')->__('PAN Number'),
                  'required' => true,
              ));
              $fieldset->addField('tin_number', 'text', array(
                  'name' => 'tin_number',
                  'label' => Mage::helper('adminhtml')->__('TIN Number'),
                  'id' => 'tin_number',
                  'value' => $profile->getTinNumber(),
                  'title' => Mage::helper('adminhtml')->__('TIN Number'),
                  'required' => true,
              ));
              $fieldset->addField('vat_number', 'text', array(
                  'name' => 'vat_number',
                  'label' => Mage::helper('adminhtml')->__('CST/VAT Number'),
                  'id' => 'vat_number',
                  'value' => $profile->getVatNumber(),
                  'title' => Mage::helper('adminhtml')->__('CST/VAT Number'),
                  'required' => true,
              ));

        }

        $fieldset = $form->addFieldset('verification_fieldset', array('legend' => Mage::helper('adminhtml')->__('Verification Information')));

        $proofList = Mage::helper('marketplace')->getVarificationProofTypeList();
        Mage::log($proofList[1],Zend_log::INFO,'loadLayout.log',true);
        if(count($proofList) > 1)
        {
          $count=0;
          foreach ($proofList as $tempProof) {
            if($count==0){
          /*  code is edited by kuldeep joshi
             $fieldset->addField('proof_type', 'text', array(
                  'name' => 'proof_type',
                  'label' => Mage::helper('adminhtml')->__('Proof Type'),
                  'title' => Mage::helper('adminhtml')->__('Proof Type'),
                  'value' => $profile->getProofType(),
                  'style' => 'display: none;',
                  'after_element_html' => $this->_getFiles($profile->getProofType(), $profile->getVarificationFiles())
                ));*/
              $count=1;
              continue;
            }

            $fieldset->addField($tempProof, 'file', array(
                'name' => $tempProof,
                'label' => Mage::helper('adminhtml')->__($tempProof),
                'title' => Mage::helper('adminhtml')->__($tempProof),
                'after_element_html' => $this->_getFiles($profile->getProofType(), $profile->getVarificationFiles())
              ));
            }
        }

        $form->setAction($this->getUrl('*/system_account/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        //return parent::_prepareForm();
        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }

    protected function _getImage($image_name) {
        if (!isset($image_name) || $image_name == '')
            return '';

        $dir_name = 'vendor' . DS . 'images';
        $dir_path = Mage::helper('marketplace')->getImagesUrl($dir_name);

        $str = '<img width="150" src="' . $dir_path . $image_name . '" alt="" style="margin-top: 10px;" /><input type="hidden" name="old_image" id="old_image" value="' . $image_name . '" />';
        $str .= '<br /><a href="javascript:void(0)" onclick="$(this).previous().remove(); $(this).previous().remove();$(this).previous().remove();$(this).previous().remove();$(this).remove();">Remove</a>';
        return $str;
    }

    protected function formatPrice($price) {
        return Mage::helper('core')->currency($price, true, false);
    }

    protected function _getCountryList()
    {
		$countries[''] = '';

		$coutryCollection = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);

		foreach($coutryCollection as $country)
			$countries[$country['value']] = $country['label'];

		return $countries;
	}

	protected function _getFiles($proofType, $fileList)
	{
		$fileListArray = json_decode($fileList, true);
		$proofTypeModel = Mage::getModel('marketplace/prooftype')->load($proofType);
		$proofName = $proofTypeModel->getName();
		if(isset($proofName))
		{
			$fileString = '';//--edited by kuldeep '<div style="margin: 0 0 1px;"><b>' . $proofName . '</b></div>';
			foreach($fileListArray as $file)
			{
				$dir_name = 'vendor' . DS . 'varifications';
				$dir_url = Mage::helper('marketplace')->getImagesUrl($dir_name);

				$fileString .= '<div style="margin: 2px 0 1px;"><a href="' . $dir_url . $file . '" target="_blank">' . $file . '</a></div>';
			}
		}
		else
			$fileString = '<div style=""><b>N/A</b></div>';
		return $fileString;
	}

}

?>

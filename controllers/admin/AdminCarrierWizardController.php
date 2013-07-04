<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCarrierWizardControllerCore extends AdminController
{
	public function __construct()
	{	
		$this->display = 'view';
		parent::__construct();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin('smartWizard');
	}
	
	public function initWizard()
	{
		$this->wizard_steps = array(
			'name' => 'carrier_wizard',
			'steps' => array(
				array(
					'title' => $this->l('General'),
					'desc' => $this->l('General'),
					),
				array(
					'title' => $this->l('Where and how much ?'),
					'desc' => $this->l('Where and how much ?'),
					),
				array(
					'title' => $this->l('What and to who ?'),
					'desc' => $this->l('What and to who ?'),
					),
				array(
					'title' => $this->l('Resume'),
					'desc' => $this->l('Resume'),
					),
					
				));
		
	/* 	$this->wizard_steps */
		
		
		
		if (Shop::isFeatureActive())
		{
			$multistore_step = array(
				array(
					'title' => $this->l('MultiStore'),
					'desc' => $this->l('MultiStore'),
				)
			);
			array_splice($this->wizard_steps['steps'], 1, 0, $multistore_step);
		}
	}
	
	public function renderView()
	{
		$this->initWizard();
		
		
		$this->tpl_view_vars = array(
			'wizard_steps' => $this->wizard_steps,
			'wizard_contents' => array(
				'contents' => array(
					0 => $this->renderStepOne(),
				)),
			'labels' => array('next' => $this->l('Next'), 'previous' => $this->l('Previous'), 'finish' => $this->l('Finish'))
			);
		
		return parent::renderView();
	}
	
	public function renderStepOne()
	{
		$fields_form = array(
			'form' => array(
			'legend' => array(
				'title' => $this->l('Carriers:'),
				'image' => '../img/admin/delivery.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Company:'),
					'name' => 'name',
					'size' => 25,
					'required' => true,
					'hint' => sprintf($this->l('Allowed characters: letters, spaces and %s'), '().-'),
					'desc' => array(
						$this->l('Carrier name displayed during checkout'),
						$this->l('For in-store pickup, enter 0 to replace the carrier name with your shop name.')
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Transit time:'),
					'name' => 'delay',
					'lang' => true,
					'required' => true,
					'size' => 41,
					'maxlength' => 128,
					'desc' => $this->l('Estimated delivery time will be displayed during checkout.')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Speed Grade:'),
					'name' => 'grade',
					'required' => false,
					'size' => 1,
					'desc' => $this->l('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.')
				),
				array(
					'type' => 'file',
					'label' => $this->l('Logo:'),
					'name' => 'logo',
					'desc' => $this->l('Upload a logo from your computer.').' (.gif, .jpg, .jpeg '.$this->l('or').' .png)'
				),
				array(
					'type' => 'text',
					'label' => $this->l('URL:'),
					'name' => 'url',
					'size' => 40,
					'desc' => $this->l('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will then be automatically replaced by the tracking number.')
				),
			)),
		);
				
		$carrier = new Carrier(1);
		$fields_value = $this->getStep1FieldsValues($carrier);
		return $this->renderGenericForm($fields_form, $fields_value);
	}
	
	public function renderStepTow($fields_form)
	{
		
		
		
	}
	
	public function renderGenericForm($fields_form, $fields_value)
	{
		$helper = new HelperForm();
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = 1;
		$this->fields_form = array();
		$helper->tpl_vars = array(
			'fields_value' => $fields_value,
			'languages' => $this->getLanguages(),
			'id_language' => 1
			);
		return $helper->generateForm(array('form' => $fields_form));
	} 
	
	public function getStep1FieldsValues($carrier)
	{
		return array(
			'name' => $this->getFieldsValue($carrier, 'name'),
			'delay' => $this->getFieldsValue($carrier, 'delay'),
			'grade' => $this->getFieldsValue($carrier, 'grade'),
			'logo' => '',
			'url' => $this->getFieldsValue($carrier, 'url'),
			);
		
	}

}
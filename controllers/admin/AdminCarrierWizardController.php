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
		$this->table = 'carrier';
		$this->identifier = 'id_carrier';
		$this->className = 'Carrier';
		$this->lang = false;
		$this->deleted = true;
		$this->step_number = 0;
		
		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 's'
		);
		
		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin('smartWizard');
		$this->addJs(_PS_JS_DIR_.'admin_carrier_wizard.js');
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

		if (Tools::getValue('id_carrier'))
			$carrier = $this->loadObject();
		else
			$carrier = new Carrier();

		$this->tpl_view_vars = array(
			'wizard_steps' => $this->wizard_steps,
			'validate_url' => $this->context->link->getAdminLink('AdminCarrierWizard'),
			'wizard_contents' => array(
				'contents' => array(
					0 => $this->renderStepOne($carrier),
					1 => $this->renderStepThree($carrier),
					2 => $this->renderStepFour($carrier),
					3 => $this->renderStepFive(),
				)),
			'labels' => array('next' => $this->l('Next'), 'previous' => $this->l('Previous'), 'finish' => $this->l('Finish'))
		);
		
		if (Shop::isFeatureActive())
			array_splice($this->tpl_view_vars['wizard_contents']['contents'], 1, 0, array(0 => $this->renderStepTwo($carrier)));

		return parent::renderView();
	}

	public function initToolbarTitle()
	{
		$bread_extended = array_unique($this->breadcrumbs);
		
		if (Tools::getValue('id_carrier'))
			$bread_extended[1] = $this->l('Edit');
		else
			$bread_extended[1] = $this->l('Add new');

		$this->toolbar_title = $bread_extended;
	}
	
	public function initToolbar()
	{
		parent::initToolbar();
		$this->toolbar_btn['back']['href'] = $this->context->link->getAdminLink('AdminCarriers');
	}
	
	public function renderStepOne($carrier)
	{
		$this->fields_form = array(
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

		$fields_value = $this->getStepOneFieldsValues($carrier);
		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
	}

	public function renderStepTwo($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Carriers:'),
					'image' => '../img/admin/delivery.gif'
				),
				'input' => array(
					array(
						'type' => 'shop',
						'label' => $this->l('Shop association:'),
						'name' => 'checkBoxShopAsso',
					),
				))
		);
		$fields_value = $this->getStepTwoFieldsValues($carrier);
		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
	}

	public function renderStepThree($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Carriers:'),
					'image' => '../img/admin/delivery.gif'
				),
				'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Apply shipping cost:'),
						'name' => 'is_free',
						'required' => false,
						'class' => 't',
						'values' => array(
							array(
								'id' => 'is_free_off',
								'value' => 0,
								'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />'
							),
							array(
								'id' => 'is_free_on',
								'value' => 1,
								'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" />'
							)
						),
						'desc' => $this->l('Apply both regular shipping cost and product-specific shipping costs.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Shipping and handling:'),
						'name' => 'shipping_handling',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'shipping_handling_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'shipping_handling_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
						'desc' => $this->l('Include the shipping and handling costs in the carrier price.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Billing:'),
						'name' => 'shipping_method',
						'required' => false,
						'class' => 't',
						'br' => true,
						'values' => array(
							array(
								'id' => 'billing_default',
								'value' => Carrier::SHIPPING_METHOD_DEFAULT,
								'label' => $this->l('Default behavior')
							),
							array(
								'id' => 'billing_price',
								'value' => Carrier::SHIPPING_METHOD_PRICE,
								'label' => $this->l('According to total price')
							),
							array(
								'id' => 'billing_weight',
								'value' => Carrier::SHIPPING_METHOD_WEIGHT,
								'label' => $this->l('According to total weight')
							)
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Tax:'),
						'name' => 'id_tax_rules_group',
						'options' => array(
							'query' => TaxRulesGroup::getTaxRulesGroups(true),
							'id' => 'id_tax_rules_group',
							'name' => 'name',
							'default' => array(
								'label' => $this->l('No Tax'),
								'value' => 0
							)
						)
					),
					array(
						'type' => 'zone',
						'name' => 'zones'
						)
					)
				));
		
		$tpl_vars = array();
		$tpl_vars['zones'] = Zone::getZones(false);
		
		$fields_value = $this->getStepThreeFieldsValues($carrier);
		// Added values of object Zone
		$carrier_zones = $carrier->getZones();
		$carrier_zones_ids = array();
		if (is_array($carrier_zones))
			foreach ($carrier_zones as $carrier_zone)
				$carrier_zones_ids[] = $carrier_zone['id_zone'];

		$zones = Zone::getZones(false);
		foreach ($zones as $zone)
			$fields_value['zones'][$zone['id_zone']] = Tools::getValue('zone_'.$zone['id_zone'], (in_array($zone['id_zone'], $carrier_zones_ids)));
		
		$shipping_method = $carrier->getShippingMethod();
		$range_table = $carrier->getRangeTable();
		$range_obj = $carrier->getRangeObject();
		
		$price_by_range = Carrier::getDeliveryPriceByRanges($range_table, (int)$carrier->id);
		
		foreach ($price_by_range as $price)
			$tpl_vars['price_by_range'][$price['id_'.$range_table]][$price['id_zone']] = $price['price'];
			
		$tmp_range = $range_obj->getRanges((int)$carrier->id);
		$tpl_vars['ranges'] = array();
		foreach ($tmp_range as $id => $range)
		{
			$tpl_vars['ranges'][$range['id_'.$range_table]] = $range;
			$tpl_vars['ranges'][$range['id_'.$range_table]]['id_range'] = $range['id_'.$range_table];
		}
		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value, $tpl_vars);
	}

	public function renderStepFour($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Carriers:'),
					'image' => '../img/admin/delivery.gif'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Out-of-range behavior:'),
						'name' => 'range_behavior',
						'options' => array(
							'query' => array(
								array(
									'id' => 0,
									'name' => $this->l('Apply the cost of the highest defined range')
								),
								array(
									'id' => 1,
									'name' => $this->l('Disable carrier')
								)
							),
							'id' => 'id',
							'name' => 'name'
						),
						'desc' => $this->l('Out-of-range behavior occurs when none is defined (e.g. when a customer\'s cart weight is greater than the highest range limit)')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Maximium package height:'),
						'name' => 'max_height',
						'required' => false,
						'size' => 10,
						'desc' => $this->l('Maximum height managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Maximium package width:'),
						'name' => 'max_width',
						'required' => false,
						'size' => 10,
						'desc' => $this->l('Maximum width managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Maximium package depth:'),
						'name' => 'max_depth',
						'required' => false,
						'size' => 10,
						'desc' => $this->l('Maximum depth managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Maximium package weight:'),
						'name' => 'max_weight',
						'required' => false,
						'size' => 10,
						'desc' => $this->l('Maximum weight managed by this carrier. Set the value to "0," or leave this field blank to ignore.')
					),
					array(
						'type' => 'group',
						'label' => $this->l('Group access:'),
						'name' => 'groupBox',
						'values' => Group::getGroups(Context::getContext()->language->id),
						'desc' => $this->l('Mark the groups that are allowed access to this carrier.')
					)
				)
			));
		$fields_value = $this->getStepFourFieldsValues($carrier);
		
		
		// Added values of object Group
		$carrier_groups = $carrier->getGroups();
		$carrier_groups_ids = array();
		if (is_array($carrier_groups))
			foreach ($carrier_groups as $carrier_group)
				$carrier_groups_ids[] = $carrier_group['id_group'];

		$groups = Group::getGroups($this->context->language->id);

		foreach ($groups as $group)
			$fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $carrier_groups_ids) || empty($carrier_groups_ids) && !$carrier->id));

		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
	}
	
	public function renderStepFive()
	{
		return $this->context->smarty->fetch('controllers/carrier_wizard/summary.tpl');
	}

	public function renderGenericForm($fields_form, $fields_value, $tpl_vars = array())
	{
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->tpl_vars = array_merge(array(
			'fields_value' => $fields_value,
			'languages' => $this->getLanguages(),
			'id_language' => $this->context->language->id
		), $tpl_vars);
		$helper->override_folder = 'carrier_wizard/';

		return $helper->generateForm($fields_form);
	}

	public function getStepOneFieldsValues($carrier)
	{
		return array(
			'id_carrier' => $this->getFieldValue($carrier, 'id_carrier'),
			'name' => $this->getFieldValue($carrier, 'name'),
			'delay' => $this->getFieldValue($carrier, 'delay'),
			'grade' => $this->getFieldValue($carrier, 'grade'),
			'logo' => '',
			'url' => $this->getFieldValue($carrier, 'url'),
		);
	}

	public function getStepTwoFieldsValues($carrier)
	{
		return array('shop' => $this->getFieldValue($carrier, 'shop'));

	}

	public function getStepThreeFieldsValues($carrier)
	{
		return array(
			'is_free' => $this->getFieldValue($carrier, 'is_free'),
			'id_tax_rules_group' => $this->getFieldValue($carrier, 'id_tax_rules_group'),
			'shipping_handling' => $this->getFieldValue($carrier, 'shipping_handling'),
			'shipping_method' => $this->getFieldValue($carrier, 'shipping_method'),
			'range_behavior' =>  $this->getFieldValue($carrier, 'range_behavior'),
			'zones' =>  $this->getFieldValue($carrier, 'zones'),
		);
	}

	public function getStepFourFieldsValues($carrier)
	{
		return array(
			'range_behavior' => $this->getFieldValue($carrier, 'shop'),
			'max_height' => $this->getFieldValue($carrier, 'max_height'),
			'max_width' => $this->getFieldValue($carrier, 'max_width'),
			'max_depth' => $this->getFieldValue($carrier, 'max_depth'),
			'max_weight' => $this->getFieldValue($carrier, 'max_weight'),
			'group' => $this->getFieldValue($carrier, 'group'),			
		);
	}
	
	public function ajaxProcessValidateStep()
	{
		$step_number = (int)Tools::getValue('step_number');
		$return = array('has_error' => false);

		if (Shop::isFeatureActive() && $step_number == 2)
		{
			if (!Tools::getValue('checkBoxShopAsso_'))
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('You must choose at least one shop or group shop.');
			}
		}
		else if ((!Shop::isFeatureActive() && $step_number == 2) || (Shop::isFeatureActive() && $step_number == 3))
		{
			
				
		}
		else
			$this->validateRules('AdminCarrierWizardControllerCore');
		

		if (count($this->errors))
		{
			$return['has_error'] = true;
			$return['errors'] = $this->errors;
		}
		die(Tools::jsonEncode($return));
	}
	
	public function processRange()
	{
		//getRangeObject
		
		$range_inf = Tools::getValue('range_inf');
		$range_sup = Tools::getValue('range_sup');
		$range_type = Tools::getValue('shipping_method');
		
		if ($range_type != Carrier::SHIPPING_METHOD_FREE)
		{
			foreach ($range_inf as $key => $range)
			{
				if ($range_type == Carrier::SHIPPING_METHOD_DEFAULT)
				{
					if (!Configuration::get('PS_SHIPPING_METHOD'))
						$range_type = Carrier::SHIPPING_METHOD_PRICE;
					else
						$range_type = Carrier::SHIPPING_METHOD_WEIGHT;
				}
				
				if ($range_type == Carrier::SHIPPING_METHOD_WEIGHT)
					$new_range = new RangeWeight();
					
				if ($range_type == Carrier::SHIPPING_METHOD_PRICE)
					$new_range = new RangePrice();
				
				
				/* $this-> */
				
				
			}	
		}
	}
	
	public function ajaxProcessFinishStep()
	{
		$return = array('has_error' => false);
		
		//TODO : check permission
				
		if ($id_carrier = Tools::getValue('id_carrier'))
		{
			$current_carrier = new Carrier((int)$id_carrier);
			// if update we duplicate current Carrier
			$carrier = $current_carrier->duplicateObject();
			if (Validate::isLoadedObject($carrier))
			{
				// Set flag deteled to true for historization
				$current_carrier->deleted = true;
				$current_carrier->update();
	
				// Fill the new carrier object
				$this->copyFromPost($carrier, $this->table);
				$carrier->position = $current_carrier->position;
				$carrier->update();
			}
		}
		else
		{
			$carrier = new Carrier();
			$this->copyFromPost($carrier, $this->table);
			if (!$carrier->add())
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('An error occurred while saving this carrier.');
			}
		}
			
		if(Validate::isLoadedObject($carrier))
		{
			if (!$this->changeGroups((int)$carrier->id))
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('An error occurred while saving carrier groups.');
			}
			
			if (!$this->changeZones((int)$carrier->id))
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('An error occurred while saving carrier zones.');
			}
			
			if (Shop::isFeatureActive() && !$this->updateAssoShop((int)$carrier->id))
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('An error occurred while saving associations of shops.');
			}
			
			if (!$carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group')))
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('An error occurred while saving the tax rules group.');
			}
			
			if (!$this->postImage((int)$carrier->id))
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('An error occurred while saving carrier logo.');
			}
			$return['id_carrier'] = $carrier->id;
		}
		die(Tools::jsonEncode($return));
	}
	
	protected function updateCarrierAsso($id_carrier)
	{
	
	}
	
	protected function changeGroups($id_carrier, $delete = true)
	{
		if ($delete)
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'carrier_group WHERE id_carrier = '.(int)$id_carrier);
		$groups = Db::getInstance()->executeS('SELECT id_group FROM `'._DB_PREFIX_.'group`');
		foreach ($groups as $group)
			if (Tools::getIsset('groupBox') && in_array($group['id_group'], Tools::getValue('groupBox')))
				return Db::getInstance()->execute('
					INSERT INTO '._DB_PREFIX_.'carrier_group (id_group, id_carrier)
					VALUES('.(int)$group['id_group'].','.(int)$id_carrier.')
				');
	}

	public function changeZones($id)
	{
		$return = true;
		$carrier = new Carrier($id);
		if (!Validate::isLoadedObject($carrier))
			die (Tools::displayError('The object cannot be loaded.'));
		$zones = Zone::getZones(false);
		foreach ($zones as $zone)
			if (count($carrier->getZone($zone['id_zone'])))
			{
				if (!isset($_POST['zone_'.$zone['id_zone']]) || !$_POST['zone_'.$zone['id_zone']])
					$return &= $carrier->deleteZone($zone['id_zone']);
			}
			else
				if (isset($_POST['zone_'.$zone['id_zone']]) && $_POST['zone_'.$zone['id_zone']])
					$return &= $carrier->addZone($zone['id_zone']);
		return $return;
	}
	
	public static function getValidationRules()
	{
		$step_number = Tools::getValue('step_number');
		$step_fields = array(
			1 => array('name', 'delay', 'grade', 'url'),
			2 => array('is_free', 'id_tax_rules_group', 'shipping_handling', 'shipping_method', 'range_behavior'),
			3 => array('range_behavior', 'max_height', 'max_width', 'max_depth', 'max_weight'),
		);

		if (Shop::isFeatureActive())
		{
			$multistore_field = array(array('shop'));
			array_splice($step_fields, 1, 0, $multistore_field);
		}

		$rules = array();
		
		if ($step_number == 1)
			$rules = Carrier::getValidationRules('Carrier');

		foreach ($rules as $key_r => $rule)
			foreach ($rule as $key_f => $field)
			{
				if (in_array($key_r, array('required', 'requiredLang')))
				{
					if(!in_array($field, $step_fields[$step_number]))
						unset($rules[$key_r][$key_f]);
				} 
				else if(!in_array($key_f, $step_fields[$step_number]))
					unset($rules[$key_r][$key_f]);
			}
		return $rules;
	}
	
	public static function displayFieldName($field)
	{
		return $field;
	}
}
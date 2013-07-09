{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2013 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="field"}
	{if $input.name == 'zones'}
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" id="range_limit" class="border_bottom border_right" style="height:80px;padding: 5px; min-width: 152px;">
					<table style="display:none" id="range_table">
						<tr>
							<td  rowspan="2" style="padding:5px;vertical-align:middle" valign="middle">
								{l s="Range limits by : "} <span >{l s="weight"}</span> <span style="display:none" id="">{l s="price"}</span>
							</td>
						</tr>
						<tr id="new_range_form_placeholder">
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
				<td class="border_bottom" style="padding:5px;">
					<a href="#" id="add_new_range">
						{l s="Add new range"}
						<img src="../img/admin/add.gif"/>
					</a>
				</td>
			</tr>
		</table>
		<table id="fees_table">
			<tr style="display:none" id="fees_all_container">
				<td>{l s="All"}</td>
				<td><input type="checkbox" name="all_zone"/></td>
			</tr>
			{foreach from=$zones item=zone}
			<tr class="zones">
				<td class="border_right" style="padding-right:10px">
					<input type="checkbox" disabled="disabled" name="zone_{$zone.id_zone}"/>
					{$zone.name}
				</td>
			</tr>
			{/foreach}
			<tr class="zones_delete">
				<td>&nbsp;</td>
			</tr>
		</table>
		
		
		<div style="display:none">
			<table id="new_range_form" cellpadding="0" cellspacing="0" class="border_bottom">
				<tr>
					<td class="border_bottom border_left" style="padding: 5px;">>=</td>
					<td><input type="text"></td>
				</tr>
				<tr>
					<td class="border_left" style="padding: 5px;"><</td>
					<td><input type="text"></td>
				</tr>
			</table>
		</div>
		
	{/if}
		{$smarty.block.parent}
{/block}

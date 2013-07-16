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
		<div style="float:left">
			<table cellpadding="5" cellspacing="0" id="zones_table">
				<tr class="range_inf">
					<td> Limites par tranche :</td>
					<td class="border_left border_bottom">>=</td>
					{foreach from=$ranges key=r item=range}
						<td class="border_bottom center"><input name="range_inf[]" type="text" value="{$range.delimiter1|string_format:"%.6f"}" /></td>
					{foreachelse}
						<td class="border_bottom center"><input name="range_inf[]" type="text" /></td>
					{/foreach}
				</tr>
				<tr class="range_sup">
					<td class="center">de poids</td>
					<td class="border_left "><</td>
					{foreach from=$ranges key=r item=range}
						<td class="center"><input name="range_sup[]" type="text" value="{$range.delimiter2|string_format:"%.6f"}" /></td>
					{foreachelse}
						<td class="center"><input name="range_sup[]" type="text" /></td>
					{/foreach}
				</tr>
				<tr class="fees_all">
					<td class="border_top border_bottom border_bold"><span class="fees_all" {if $ranges|count == 0}style="display:none" {/if}>All</span></td>
					<td></td>
					{foreach from=$ranges key=r item=range}
						<td class="center border_top border_bottom">
							<input type="text" />
						</td>
					{foreachelse}
						<td class="center border_top border_bottom">
							<input style="display:none" type="text" />
							<button class="button">{l s="Validate"}</button>
						</td>
					{/foreach}
				</tr>
				{foreach from=$zones key=i item=zone}
				<tr class="fees {if $i is odd}alt_row{/if}" data-zoneid="{$zone.id_zone}">
					<td>{$zone.name}</td>
					<td class="zone"><input class="input_zone" name="zone_{$zone.id_zone}" value="1" type="checkbox" {if isset($fields_value[$input.name][$zone.id_zone])} checked="checked"{/if}/></td>
					{foreach from=$ranges key=r item=range}
						<td class="center"><input {if !isset($price_by_range[$range.id_range][$zone.id_zone])} disabled="disabled" {/if} name="fees[{$zone.id_zone}][]" type="text" value="{if isset($price_by_range[$range.id_range][$zone.id_zone])} {$price_by_range[$range.id_range][$zone.id_zone]|string_format:"%.6f"} {/if}" /></td>
					{/foreach}
				</tr>
				{/foreach}
				<tr class="delete_range">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					{foreach from=$ranges name=ranges key=r item=range}
						{if $smarty.foreach.ranges.first}
							<td class="center">&nbsp;</td>
						{else}
							<td class="center"><button class="button">{l s="Delete"}</button</td>
						{/if}
					{/foreach}
				</tr>
			</table>
		</div>
		<div class="new_range">
			<a href="#" onclick="add_new_range();return false;" class="button" id="add_new_range">{l s="Add new range"}<img src="../img/admin/add.gif"/></a>
		</div>
	{/if}
		{$smarty.block.parent}
{/block}

/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


$(document).ready(function() {
	
	bind_inputs();
	initCarrierWizard();
	
});

function initCarrierWizard()
{
	$("#carrier_wizard").smartWizard({
		'labelNext' : labelNext,
		'labelPrevious' : labelPrevious,
		'labelFinish' : labelFinish,
		'fixHeight' : 1,
		'onShowStep' : onShowStepCallback,
		'onLeaveStep' : leaveStepCallback,
	});
}

function onShowStepCallback()
{
	$('.anchor li a').each( function () {
		$(this).parent('li').addClass($(this).attr('class'));
	});
	resizeWizard();
}

function leaveStepCallback(obj, context)
{
	return validateSteps(context.fromStep); // return false to stay on step and true to continue navigation 
}

function validateSteps(step_number)
{
	$('.wizard_error').remove();
	var is_ok = true;
	form = $('#carrier_wizard #step-'+step_number+' form');
	$.ajax({
		type:"POST",
		url : validate_url,
		async: false,
		dataType: 'json',
		data : form.serialize()+'&step_number='+step_number+'&action=validate_step&ajax=1',
		success : function(datas)
		{
			if (datas.has_error)
			{
				is_ok = false;
				
				$('input').focus( function () {
					$(this).removeClass('field_error');
				});
				
				str_error = '<div class="error wizard_error"><span style="float:right"><a id="hideError" href="#"><img alt="X" src="../img/admin/close.png" /></a></span><ul>';
				for (var error in datas.errors)
				{
					$('#carrier_wizard').smartWizard('setError',{stepnum:step_number,iserror:true});
					$('input[name="'+error+'"]').addClass('field_error');
					str_error += '<li>'+datas.errors[error]+'</li>';
				}
				$('#step-'+step_number).prepend(str_error+'</ul></div>');
				resizeWizard();
			}
		}
	});
	return is_ok;
}

function resizeWizard()
{
	resizeInterval = setInterval(function (){$("#carrier_wizard").smartWizard('fixHeight'); clearInterval(resizeInterval)}, 100);
}

function bind_inputs()
{
	$('tr.delete_range td button').off('click').on('click', function () {
		index = $(this).parent('td').index();
		$('tr.range_sup td:eq('+index+'), tr.range_inf td:eq('+index+'), tr.fees_all td:eq('+index+'), tr.delete_range td:eq('+index+')').remove();
		$('tr.fees').each( function () {
			$(this).children('td:eq('+index+')').remove();
		});
		return false;
	});
	
	$('tr.fees_all td button').off('click').on('click', function () {
		index = $(this).parent('td').index();
		if (validateRange(index))
			enableRange(index);
		else
			disableRange(index);
		return false;
	});
	
	$('tr.fees td input:checkbox').off('change').on('change', function () {
				
		if($(this).is(':checked'))
		{
			$(this).closest('tr').children('td').each( function (){
				index = $(this).index();
				if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
					$(this).children('input:text').removeAttr('disabled');
			});
		}
		else
			$(this).closest('tr').children('td').children('input:text').attr('disabled', 'disabled');
		return false;
	});
	
	$('tr.range_sup td input:text, tr.range_inf td input:text').focus( function () {
		$(this).removeClass('field_error');
	});
	
	$('tr.range_sup td input:text, tr.range_inf td input:text').off('change').on('change', function () {
		index = $(this).parent('td').index();
		
		if ($('tr.fees_all td:eq('+index+')').hasClass('validated') || $('tr.fees_all td:eq('+index+')').hasClass('not_validated'))
		{
			if (validateRange(index))
				enableRange(index);
			else
				disableRange(index);
		}
	});
	
	$('tr.fees_all td input').off('change').on('change', function () {
		index = $(this).parent('td').index();
		val = $(this).val();
		$('tr.fees td input:text').not('disabled').val(val);
		
		return false;
	});
}

function validateRange(index)
{
	//reset error css
	$('tr.range_sup td input:text').removeClass('field_error');
	$('tr.range_inf td input:text').removeClass('field_error');
	
	is_ok = true;
	range_sup = parseInt($('tr.range_sup td:eq('+index+')').children('input:text').val().trim());
	range_inf = parseInt($('tr.range_inf td:eq('+index+')').children('input:text').val().trim());

	if (isNaN(range_sup) || range_sup.length === 0)
	{
		$('tr.range_sup td:eq('+index+')').children('input:text').addClass('field_error');
		is_ok = false;
	}
	
	if (isNaN(range_inf) || range_inf.length === 0)
	{
		$('tr.range_inf td:eq('+index+')').children('input:text').addClass('field_error');
		is_ok = false;
	}
	
	if (is_ok)
	{
		if (range_inf >= range_sup)
		{
			$('tr.range_sup td:eq('+index+')').children('input:text').addClass('field_error');
			$('tr.range_inf td:eq('+index+')').children('input:text').addClass('field_error');
			is_ok = false;
		}
		//check if previous range is inf only if it's not the first range
		if (index > 2)
		{
			previous_range_sup = parseInt($('tr.range_sup td:eq('+(index -1)+')').children('input:text').val().trim());
			console.log(range_inf+' < '+previous_range_sup);
			if (range_inf < previous_range_sup)
			{
				$('tr.range_inf td:eq('+index+')').children('input:text').addClass('field_error');
			}
		}
		//check if next range is sup only if it's not the last range
		if ($('tr.range_inf td:eq('+(index + 1)+')').length)
		{
			next_range_inf = parseInt($('tr.range_inf td:eq('+(index +1)+')').children('input:text').val().trim());

			if ((isNaN(range_sup) || range_sup.length === 0) && range_sup > next_range_inf)
			{
				$('tr.range_sup td:eq('+index+')').children('input:text').addClass('field_error');
			}
		}
		
	}
	return is_ok;
}

function enableRange(index)
{
	$('tr.fees').each( function () {
		//only enable fees for enabled zones
		if ($(this).children('td').children('input:checkbox').attr('checked') == 'checked')
			$(this).children('td:eq('+index+')').children('input').removeAttr('disabled');
	});
	$('span.fees_all').show();
	$('tr.fees_all td:eq('+index+')').children('input').show().removeAttr('disabled');
	$('tr.fees_all td:eq('+index+')').addClass('validated').removeClass('not_validated');
	$('tr.fees_all td:eq('+index+')').children('button').remove();
}

function disableRange(index)
{
	$('tr.fees').each( function () {
		//only enable fees for enabled zones
		if ($(this).children('td').children('input:checkbox').attr('checked') == 'checked')
			$(this).children('td:eq('+index+')').children('input').attr('disabled', 'disabled');
	});
	$('tr.fees_all td:eq('+index+')').children('input').attr('disabled', 'disabled');
	$('tr.fees_all td:eq('+index+')').removeClass('validated').addClass('not_validated');
}

function add_new_range()
{
	//add new rand sup input
	$('tr.range_sup td:last').after('<td class="center"><input name="range_sup[]" type="text" /></td>');
	//add new rand inf input
	$('tr.range_inf td:last').after('<td class="border_bottom center"><input name="range_inf[]" type="text" /></td>');
	
	$('tr.fees_all td:last').after('<td class="center border_top border_bottom"><input style="display:none" type="text" /> <button class="button">'+labelValidate+'</button</td>');

	$('tr.fees').each( function () {
		$(this).children('td:last').after('<td><input disabled="disabled" name="fees['+$(this).data('zoneid')+'][]" type="text" /></td>');
	});
	$('tr.delete_range td:last').after('<td class="center"><button class="button">'+labelDelete+'</button</td>');
	
	resizeWizard();
	bind_inputs();
	return false;
}

function delete_new_range()
{
	if ($('#new_range_form_placeholder').children('td').length = 1)
		return false;
}

function setInputRangeId()
{
	
	
}
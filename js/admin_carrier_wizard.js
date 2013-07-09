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

function validateSteps(step_number){
    var step_valid = true;
	
	form = $('#carrier_wizard #step-'+step_number+' form');
	
/*
	switch(step_number)
	{
		case 1:
	
			break;
		case 2:
	
			break;
		default:
		return false;
	}
*/
	
	$.ajax({
		type:"POST",
		url : validate_url,
		async: true,
		dataType: 'json',
		data : form.serialize()+'&step_number='+step_number+'&action=validate_step&ajax=1',
		success : function(datas)
		{
			if (datas.has_error)
			{
				for (var error in datas.errors)
				{
					$('#carrier_wizard').smartWizard('setError',{stepnum:step_number,iserror:true});
					$('input[name="'+error+'"]').css({'border' : 'solid 1px red', 'background-color' : '#FFCCCC'});
					$("#carrier_wizard")

				}
				return false;
			}
			
		}
	});
	return true;
}

function resizeWizard()
{
	resizeInterval = setInterval(function (){$("#carrier_wizard").smartWizard('fixHeight'); clearInterval(resizeInterval)}, 100);
}

function bind_inputs()
{
	$('a#add_new_range').bind('click', function () {
		add_new_range();
	});
	
	$('tr.zones_delete td button').each( function () {
		$(this).bind('click', function () {

			return false;
		});
	});
}


function add_new_range()
{
	$('.zones td input:checkbox').removeAttr('disabled');
	$('td#range_limit').children('table').show();
	$('tr#new_range_form_placeholder').children('td:last').after('<td><table cellpadding="0" cellspacing="0">'+$('#new_range_form').html()+'</table></td>');
	$('tr.zones').each( function () {
		$(this).children('td:eq(1)').removeClass('border_right');
		$(this).children('td:last').after('<td class="border_right" style="width:173px;text-align:center"><input type="text" disabled="disabled"/></td>');
	});
	$('tr.zones_delete:last').children('td:last').after('<td class="border_right" style="width:173px;text-align:center"><button class="button">'+labelDelete+'</button></td>');
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
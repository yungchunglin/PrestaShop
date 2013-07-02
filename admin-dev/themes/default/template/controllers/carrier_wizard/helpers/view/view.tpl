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

{extends file="helpers/view/view.tpl"}
	
{block name="override_tpl"}

<script>
$(document).ready(function () {
	$('#carrier_wizard').smartWizard();
});
</script>

<div id="carrier_wizard" class="swMain">
<ul>
	<li>
		<a href="#step-1">
			<label class="stepNumber">1</label>
			<span class="stepDesc">Step 1<br /><small>Step 1 description</small></span>
		</a>
	</li>
	<li>
		<a href="#step-2">
			<label class="stepNumber">2</label>
			<span class="stepDesc">Step 2<br /><small>Step 2 description</small></span>
		</a>
	</li>
	<li>
		<a href="#step-3">
			<label class="stepNumber">3</label>
			<span class="stepDesc">Step 3<br /><small>Step 3 description</small></span> 									
		</a>
	</li>
	<li>
		<a href="#step-4">
			<label class="stepNumber">4</label>
			<span class="stepDesc">Step 4<br /><small>Step 4 description</small></span> 									
		</a>
	</li>
</ul>
<div id="step-1"> 	
		<h2 class="StepTitle">Step 1 Content</h2>
			<!-- step content -->
</div>
<div id="step-2">
		<h2 class="StepTitle">Step 2 Content</h2> 
			<!-- step content -->
</div>											
<div id="step-3">
		<h2 class="StepTitle">Step 3 Title</h2> 	
			<!-- step content -->
</div>
<div id="step-4">
		<h2 class="StepTitle">Step 4 Title</h2> 	
			<!-- step content --> 												
</div>
</div>
{/block}

<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://justinwhall.com
 * @since      1.0.0
 *
 * @package    Stripe_Card_Reminder
 * @subpackage Stripe_Card_Reminder/admin/partials
 */
?>

<span>Find customers whos cards expire by</span> 
<input placeholder="xx/xx/xxx" type="text" value="" class="scr-date-picker">

<p class="submit">
	<input type="button"  name="scr-submit-report" id="scr-submit-report" class="button button-primary" value="Run Report">
</p>
<div class="scr-loading">
	<img id="scr-loader" src="/wp-admin/images/spinner.gif"> <p>Checking subscribers. This may take a while. Do not leave this page.</p>
</div>

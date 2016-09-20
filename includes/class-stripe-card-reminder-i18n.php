<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://justinwhall.com
 * @since      1.0.0
 *
 * @package    Stripe_Card_Reminder
 * @subpackage Stripe_Card_Reminder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Stripe_Card_Reminder
 * @subpackage Stripe_Card_Reminder/includes
 * @author     Justin W HAll <justin@windsorup.com>
 */
class Stripe_Card_Reminder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'stripe-card-reminder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
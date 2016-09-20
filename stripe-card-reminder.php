<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://justinwhall.com
 * @since             1.0.0
 * @package           Stripe_Card_Reminder
 *
 * @wordpress-plugin
 * Plugin Name:       Stripe Expired Card Reminder
 * Plugin URI:        http://justinwhall.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Justin W Hall
 * Author URI:        http://justinwhall.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       stripe-card-reminder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SCR_DIR_PATH', dirname(__FILE__).'/' ); 

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-stripe-card-reminder-activator.php
 */
function activate_stripe_card_reminder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-stripe-card-reminder-activator.php';
	Stripe_Card_Reminder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-stripe-card-reminder-deactivator.php
 */
function deactivate_stripe_card_reminder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-stripe-card-reminder-deactivator.php';
	Stripe_Card_Reminder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_stripe_card_reminder' );
register_deactivation_hook( __FILE__, 'deactivate_stripe_card_reminder' );

/**
 * Extend WC Email class
 */
function stripe_reminder_email(){
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-stripe-card-reminder-email.php';
	$email_classes['WC_Card_Reminder_Email'] = new WC_Card_Reminder_Email();

	return $email_classes;
}
add_action( 'woocommerce_email_classes', 'stripe_reminder_email', 10, 1 );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-stripe-card-reminder.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_stripe_card_reminder() {

	$plugin = new Stripe_Card_Reminder();
	$plugin->run();

}
run_stripe_card_reminder();

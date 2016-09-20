<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://justinwhall.com
 * @since      1.0.0
 *
 * @package    Stripe_Card_Reminder
 * @subpackage Stripe_Card_Reminder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Stripe_Card_Reminder
 * @subpackage Stripe_Card_Reminder/admin
 * @author     Justin W HAll <justin@windsorup.com>
 */
class Stripe_Card_Reminder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $headers;
	public $url = 'https://api.stripe.com/v1/';
	public $fields = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$scr_options = get_option( 'scr_options');
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->headers = array('Authorization: Bearer ' . $scr_options['src_stripe_api_key']); 

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if (isset($_GET['page']) && ($_GET['page'] == 'stripe-card-reminder')) {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/stripe-card-reminder-admin.css', array(), $this->version, 'all');
			wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), $this->version, 'all');
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if (isset($_GET['page']) && ($_GET['page'] == 'stripe-card-reminder')) {
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/stripe-card-reminder-admin.js', array('jquery', 'wp-util'), $this->version, false);
			wp_localize_script($this->plugin_name, 'ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('scr_nonce') ) );
		}

	}

	/**
	 * Adds setting page
	 */
	public function add_settings_menu() {
		add_submenu_page('woocommerce', 'Stripe Card Reminder', 'Stripe Card Reminder', 'manage_options', 'stripe-card-reminder', array($this, 'render_options_page'));
	}

	/**
	 * Renders the options page
	 * @return void
	 */
	public function render_options_page() {
		$this->options = get_option('scr_options');
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
		?>
		<div class="wrap">
			<?php if ( class_exists( 'WooCommerce' ) ):  ?>
				<h2 class="nav-tab-wrapper">
				    <a href="?page=stripe-card-reminder&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
				    <a href="?page=stripe-card-reminder&tab=run_report" class="nav-tab <?php echo $active_tab == 'run_report' ? 'nav-tab-active' : ''; ?>">Run Report</a>
				    <a href="?page=stripe-card-reminder&tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>">Email</a>
				</h2>
			    <form method="post" action="options.php">
			    <?php
					
					// This prints out all hidden setting fields
					if ($active_tab === 'settings') {

						settings_fields('scr_settings_group');
						do_settings_sections('scr-setting-admin');
						submit_button();

					} else if ($active_tab === 'email') {

						printf( '<p>Email seetings are <a href="/wp-admin/admin.php?page=wc-settings&tab=email&section=wc_card_reminder_email">here</a></p>' );

					} else if ($active_tab === 'run_report') {

						do_settings_sections('scr-setting-run-report'); ?>

						<div id="scr-results" >
							<div class="card">
							   <div class="single-customer-header scr-tr">
							   		<span class="scr-tc customer-name"><strong>Customer Name</strong></span>
							   		<span class="scr-tc customer-email"><strong>Customer Email</strong></span>
							   		<span class="scr-tc customer-order-id"><strong>Order ID</strong></span>
						   		</div>
						   		<div class="customers"></div>
					   		</div>
					   		<p class="submit">
								<input type="button"  name="scr-email-customers" id="scr-email-customers" class="button button-primary" value="Email Customers">
								<img id="scr-email-loader" src="/wp-admin/images/spinner.gif">
								<span id="scr-email-success">
									email success!
								</span>
							</p>
						</div>

						<div id="scr-no-results" class="card">No customers cards expire by that date</div>
						
						<script type="text/html" id="tmpl-customers">
						   <div class="single-customer scr-tr">
						   		<span class="scr-tc customer-name">{{{data.name}}}</span>
						   		<span class="scr-tc customer-email">{{{data.email}}}</span>
						   		<span class="scr-tc customer-order-id">{{{data.order_id}}}</span>
					   		</div>
						</script>

						<?php
					}

				?>
			    </form>
			    <?php else: ?>
				    <div class="card"> WooCommerce needs to be active for this plugin to work </div>
		    	<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Registers settings
	 * @return void
	 */
	public function scr_register_settings() {
		register_setting(
			'scr_settings_group',
			'scr_options',
			array($this, 'sanitize')
		);

		add_settings_section(
			'scr_options',
			'Stripe Expired Card Reminder',
			array($this, 'print_section_info'),
			'scr-setting-admin'
		);

		add_settings_section(
			'scr_options',
			'Stripe Run Report',
			array($this, 'scr_section_run_report'),
			'scr-setting-run-report'
		);

		add_settings_field(
			'api_key',
			'Stripe API Key',
			array($this, 'src_stripe_api_key'),
			'scr-setting-admin',
			'scr_options'
		);

	}

	/**
	 * View for running customer report on settings page
	 * @return void
	 */
	public function scr_section_run_report() {
		include plugin_dir_path(__FILE__) . 'partials/view-run-report.php';
	}

	/**
	 * Print API notice
	 */
	public function print_section_info() {
		print 'Learn how to get your API key <a target="blank" href="http://justinwhall.com/contact/stripe-credit-card-expire-reminder-wordpress-plugin">here</a>.';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function src_stripe_api_key() {
		printf(
			'<input type="text" id="src_stripe_api_key" name="scr_options[src_stripe_api_key]" value="%s" />',
			isset($this->options['src_stripe_api_key']) ? esc_attr($this->options['src_stripe_api_key']) : ''
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize($input) {
		$new_input = array();

		if (isset($input['src_stripe_api_key'])) {
			$new_input['src_stripe_api_key'] = sanitize_text_field($input['src_stripe_api_key']);
		}

		return $new_input;
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {
		$settings = array(
			'section_title' => array(
				'name' => __('Section Title', 'woocommerce-settings-tab-demo'),
				'type' => 'title',
				'desc' => '',
				'id' => 'wc_stripe_card_reminder_section_title',
			),
			'section_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_stripe_card_reminder_section_end',
			),
		);
		return apply_filters('wc_stripe_card_reminder_settings', $settings);
	}

	/**
	 * Makes a call to the Stripe API.
	 * @param  string $url URL to use for API call.
	 * @return array  Array response from Stripe.
	 */
	public function call_stripe($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);
		curl_close($ch);

		return json_decode($output, true); // return php array with api response
	}

	/**
	 * AJAC function that query's customers who have cards that are soon-2-expire
	 * @return JSON object of customers
	 */
	public function scr_run_report() {
		check_ajax_referer('scr_nonce', 'nonce');
		
		$date_check = $_GET['searchDate'];
		$date_check = strtotime($date_check);
		$subscriptions = WC_Subscriptions_Manager::get_all_users_subscriptions();
		$customers_2_notify = array();

		if ( is_array( $subscriptions ) ) {

			foreach ( $subscriptions as $key => $sub ) {
				
				$sub_meta = get_post_meta( $sub['order_id'] );
				$url = 'https://api.stripe.com/v1/customers/' . $sub_meta['_stripe_customer_id'][0];
				
				// no need to check of subscription isn't active...
				if ( $sub['status'] === 'active' ) {
					
					$customer = $this->call_stripe( $url );
					
					// Make sure the customer exists in their stripe account
					if (!array_key_exists( 'error', $customer ) && is_array( $customer['sources']['data'] ) && count( $customer['sources']['data'] ) ) {
						
						// Possible for a customer to have more than on credit card on file.
						$customer_is_current = true;
						$customer_meta = array();

						foreach ( $customer['sources']['data'] as $payment_method ) {

							if ( $payment_method['object'] === 'card' ) {

								$card_ex = '01/' . $payment_method['exp_month'] . '/' . $payment_method['exp_year'];
								$card_n = $card_ex;
								$card_ex = strtotime($card_ex);

								// check if expire date is after date set by user
								if ($card_ex < $date_check) {
									
									$customer_is_current = false;

								}
								
								if ( !$customer_is_current ) {
									
									$customer_name = $sub_meta['_billing_first_name'][0] . ' '. $sub_meta['_billing_last_name'][0];
									$customer_meta['name'] = $customer_name;
									$customer_meta['email'] = $sub_meta['_billing_email'][0];
									$customer_meta['order_id'] = $sub['order_id'];

									if ( !array_key_exists( $customer_name, $customer_meta ) ) {
										
										$customers_2_notify[$customer_name] = $customer_meta;

									}
								}
							}
						}
					}
				}
			}
		}

		// return false if there are no customers to notify
		$return = count( $customers_2_notify ) ? $customers_2_notify : false;

		echo wp_send_json( $return );

	}

	/**
	 * Builds emails to notify customers
	 * @return void
	 */
	public function scr_build_email() {
		check_ajax_referer('scr_nonce', 'nonce');
		global $woocommerce;
		
		$mailer = $woocommerce->mailer();
		$scr_email = new WC_Card_Reminder_Email();
		$scr_email->send_email( $_POST['customers'] );

		die;
	}

}


<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * A custom Card Reminder WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Card_Reminder_Email extends WC_Email {
	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {
		// set ID, this simply needs to be a unique name
		$this->id = 'stripe_card_reminder';
		// this is the title in WooCommerce Email settings
		$this->title = 'Stripe Credit Card Expiration Reminder';
		// this is the description in WooCommerce email settings
		$this->description = 'Card Expriation Notification emails are sent to customers with an active subscription who have a credit card that is expiring soon. Works with Stripe Only.';
		// these are the default heading and subject lines that can be overridden using the settings
		$this->heading = 'Your Card On Record Expires Soon';
		$this->subject = 'Your Card On Record Expires Soon';
		$this->body = 'Oh no, it looks like your credit card on file is expiring soon! Please <a href="' . get_site_url() . '/my-account/">log in</a> to your account and update your billing information.';
		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  = 'emails/admin-new-order.php';
		$this->template_plain = 'emails/plain/admin-new-order.php';
		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();
		// this sets the recipient to the settings defined below in init_form_fields()
		$this->recipient = $this->get_option( 'recipient' );
		// if none was entered, just use the WP admin email as a fallback
		if ( ! $this->recipient )
			$this->recipient = get_option( 'admin_email' );

	}
	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 0.1
	 * @param int $order_id
	 */
	public function send_email( $emails ) {
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		if ( !strlen( $this->get_option( 'body' ) ) ) {
			$body = $this->body;
		} else {
			$body = $this->get_option( 'body' );
		}

		$message = WC_Emails::wrap_message( $this->get_subject(), $body );
		
		foreach ( $emails as $email ) {
			$this->send( $email, $this->get_subject(), $message );
		}

	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 2.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => 'Recipient(s)',
				'type'        => 'text',
				'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => 'Subject',
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => 'Email Heading',
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'body'    => array(
				'title'       => 'Message Body',
				'type'        => 'textarea',
				'description' => sprintf( __( 'This controls the body of the message. Leave blank to use: <code>%s</code>.' ), $this->body ),
				'placeholder' => '',
				'default'     => $this->body
			),
			'email_type' => array(
				'title'       => 'Email type',
				'type'        => 'select',
				'description' => 'Choose which format of email to send.',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woocommerce' ),
					'html' 	    => __( 'HTML', 'woocommerce' ),
					'multipart' => __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}
}
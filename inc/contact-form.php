<?php
/**
 * Contact form handler — admin-post.php action `ogc_contact_submit`.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed subject/category values (must match page-contact.php select options).
 *
 * @return string[]
 */
function ogc_contact_categories() {
	return array(
		'general'                       => __( 'General inquiry', 'ogc-newfinity' ),
		'software-app-development'      => __( 'Software & App Development', 'ogc-newfinity' ),
		'custom-wordpress-development'  => __( 'Custom WordPress Development', 'ogc-newfinity' ),
		'ui-ux-design'                  => __( 'UI/UX Design', 'ogc-newfinity' ),
		'marketing-strategy-consulting' => __( 'Marketing & Strategy Consulting', 'ogc-newfinity' ),
		'cybersecurity'                 => __( 'Cybersecurity', 'ogc-newfinity' ),
		'ai-solutions'                  => __( 'AI Solutions', 'ogc-newfinity' ),
	);
}

/**
 * Redirect helper back to the Contact page with a status query flag.
 *
 * @param string $status 'sent' or 'error'.
 */
function ogc_contact_redirect( $status ) {
	$url = add_query_arg( $status, '1', home_url( '/contact/' ) );
	wp_safe_redirect( $url );
	exit;
}

/**
 * Handle Contact form submissions (logged-in and logged-out).
 */
function ogc_handle_contact_submit() {
	if ( ! isset( $_POST['ogc_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ogc_contact_nonce'] ) ), 'ogc_contact_submit' ) ) {
		ogc_contact_redirect( 'error' );
	}

	/* Honeypot — bots fill this; humans never see it. Silent reject. */
	$honeypot = isset( $_POST['ogc_company_url'] ) ? sanitize_text_field( wp_unslash( $_POST['ogc_company_url'] ) ) : '';
	if ( '' !== $honeypot ) {
		ogc_contact_redirect( 'sent' );
	}

	$name    = isset( $_POST['ogc_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ogc_name'] ) ) : '';
	$email   = isset( $_POST['ogc_email'] ) ? sanitize_email( wp_unslash( $_POST['ogc_email'] ) ) : '';
	$subject = isset( $_POST['ogc_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['ogc_subject'] ) ) : '';
	$message = isset( $_POST['ogc_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ogc_message'] ) ) : '';

	$categories = ogc_contact_categories();

	if ( '' === $name || ! is_email( $email ) || '' === $message || ! isset( $categories[ $subject ] ) ) {
		ogc_contact_redirect( 'error' );
	}

	$category_label = $categories[ $subject ];
	$mail_subject   = sprintf(
		/* translators: %s: inquiry category label */
		__( '[OGC NewFinity Contact] %s', 'ogc-newfinity' ),
		$category_label
	);

	$body  = "Name: {$name}\n";
	$body .= "Email: {$email}\n";
	$body .= "Category: {$category_label}\n\n";
	$body .= "Message:\n{$message}\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( 'contact@ogcnewfinity.com', $mail_subject, $body, $headers );

	ogc_contact_redirect( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_ogc_contact_submit', 'ogc_handle_contact_submit' );
add_action( 'admin_post_nopriv_ogc_contact_submit', 'ogc_handle_contact_submit' );

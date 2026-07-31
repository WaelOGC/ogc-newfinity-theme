<?php
/**
 * Submit Your Idea form handler — admin-post.php action `ogc_submit_idea`.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed category values (must match the final 6-service list).
 *
 * @return string[]
 */
function ogc_submit_idea_categories() {
	return array(
		'software-app-development'      => __( 'Software & App Development', 'ogc-newfinity' ),
		'custom-wordpress-development'  => __( 'Custom WordPress Development', 'ogc-newfinity' ),
		'ui-ux-design'                  => __( 'UI/UX Design', 'ogc-newfinity' ),
		'marketing-strategy-consulting' => __( 'Marketing & Strategy Consulting', 'ogc-newfinity' ),
		'cybersecurity'                 => __( 'Cybersecurity', 'ogc-newfinity' ),
		'ai-solutions'                  => __( 'AI Solutions', 'ogc-newfinity' ),
	);
}

/**
 * Redirect helper back to Submit Your Idea with a status query flag.
 *
 * @param string $status 'sent' or 'error'.
 */
function ogc_submit_idea_redirect( $status ) {
	$url = add_query_arg( $status, '1', home_url( '/submit-idea/' ) );
	wp_safe_redirect( $url );
	exit;
}

/**
 * Handle Submit Your Idea form submissions (logged-in and logged-out).
 */
function ogc_handle_submit_idea() {
	if ( ! isset( $_POST['ogc_submit_idea_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ogc_submit_idea_nonce'] ) ), 'ogc_submit_idea' ) ) {
		ogc_submit_idea_redirect( 'error' );
	}

	/* Honeypot — bots fill this; humans never see it. Silent reject. */
	$honeypot = isset( $_POST['ogc_hp_token'] ) ? sanitize_text_field( wp_unslash( $_POST['ogc_hp_token'] ) ) : '';
	if ( '' !== $honeypot ) {
		ogc_submit_idea_redirect( 'sent' );
	}

	/* Time trap — submissions under 2s are almost never human. */
	$rendered_at = isset( $_POST['ogc_submit_idea_ts'] ) ? absint( wp_unslash( $_POST['ogc_submit_idea_ts'] ) ) : 0;
	$elapsed     = $rendered_at > 0 ? ( time() - $rendered_at ) : 0;
	if ( $rendered_at > 0 && $elapsed < 2 ) {
		ogc_submit_idea_redirect( 'error' );
	}

	$title       = isset( $_POST['ogc_idea_title'] ) ? sanitize_text_field( wp_unslash( $_POST['ogc_idea_title'] ) ) : '';
	$category    = isset( $_POST['ogc_idea_category'] ) ? sanitize_text_field( wp_unslash( $_POST['ogc_idea_category'] ) ) : '';
	$description = isset( $_POST['ogc_idea_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ogc_idea_description'] ) ) : '';

	if ( ogc_message_contains_url( $description ) ) {
		ogc_submit_idea_redirect( 'error' );
	}

	$categories = ogc_submit_idea_categories();

	if ( '' === $title || '' === $description || ! isset( $categories[ $category ] ) ) {
		ogc_submit_idea_redirect( 'error' );
	}

	$category_label = $categories[ $category ];
	$mail_subject   = sprintf(
		/* translators: %s: project title */
		__( '[OGC NewFinity Submit Idea] %s', 'ogc-newfinity' ),
		$title
	);

	$body  = "Project Title: {$title}\n";
	$body .= "Category: {$category_label}\n\n";
	$body .= "Description:\n{$description}\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
	);

	$sent = wp_mail( 'contact@ogcnewfinity.com', $mail_subject, $body, $headers );

	ogc_submit_idea_redirect( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_ogc_submit_idea', 'ogc_handle_submit_idea' );
add_action( 'admin_post_nopriv_ogc_submit_idea', 'ogc_handle_submit_idea' );

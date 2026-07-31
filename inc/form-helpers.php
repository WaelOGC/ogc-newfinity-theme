<?php
/**
 * Shared form helpers (anti-spam / validation used by multiple theme forms).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether free-text contains a URL or URL-like domain (anti-spam).
 *
 * Matches explicit schemes (http/https), www. prefixes, and bare domains with
 * a real TLD — not short abbreviations like "e.g." or dotted version numbers.
 * Adjust the pattern here if legitimate messages are false-positiving.
 *
 * Shared by Contact Message and Submit Idea Description fields.
 *
 * @param string $message Sanitized free-text.
 * @return bool True if a URL-like pattern is found.
 */
function ogc_message_contains_url( $message ) {
	$message = (string) $message;
	if ( '' === $message ) {
		return false;
	}

	/*
	 * 1) Explicit URL schemes or www.
	 * 2) Bare domain: label.tld with a known/common TLD (first label ≥ 2 chars
	 *    so "e.g." does not match). Adjust the TLD list if needed.
	 */
	$pattern = '/(?:https?:\/\/|www\.)|(?:\b[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.(?:com|net|org|io|ru|xyz|info|biz|nl|eu|co|uk|de|fr|app|dev|ai|me|tv|cc|us|be|es|it|ch|at|se|no|dk|fi|pl|cz|pt|ie|au|ca|jp|cn|in|br)\b)/i';

	return (bool) preg_match( $pattern, $message );
}

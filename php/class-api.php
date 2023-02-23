<?php
/**
 * API class.
 *
 * Haveipwned API request handler.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Class for haveipwned API
 */
class Api {

	/**
	 * Get API Key.
	 *
	 * @return string
	 */
	public function get_key() {
		$option = get_option( Settings::OPTION_NAME );
		return $option['api_key'] ?? '';
	}

	/**
	 * Check if email address is pwned.
	 *
	 * @param string $email Email address to check.
	 *
	 * @return string Breach data.
	 */
	public function get_breaches( $email ) {
		$url  = sprintf( 'https://haveibeenpwned.com/api/v3/breachedaccount/%s?truncateResponse=false', urlencode( trim( $email ) ) );
		$args = array(
			'user-agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
			'headers'    => array(
				'hibp-api-key' => $this->get_key(),
			),
		);

		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			Utils::log( $response->get_error_code() );
		}

		return wp_remote_retrieve_body( $response );
	}
}

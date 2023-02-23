<?php
/**
 * Notification class.
 *
 * Notify handler.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Notification class
 */
class Notification {

	/**
	 * Send notification.
	 *
	 * @param string $message Message to send.
	 */
	public function send( $message ) {
		$this->send_email( $message );
		// $this->send_sms( $message );
	}

	/**
	 * Get notify emails string in comma separated format.
	 *
	 * @return string
	 */
	private function get_notify_emails() {
		$option = get_option( Settings::OPTION_NAME );
		return $option['notify_emails'] ?? '';
	}

	/**
	 * Send email notification.
	 *
	 * @param string $message Notification message to send.
	 */
	private function send_email( $message ) {
		$notify_emails = $this->get_notify_emails();
		if ( empty( $notify_emails ) ) {
			return;
		}

		$subject = __( 'Pwned Checker Notification', 'pwned-checker' );

		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
		wp_mail( $notify_emails, $subject, $message );
	}

	/**
	 * Send SMS notification. Integrate twillo API.
	 *
	 * @param string $message 
	 */
	private function send_sms( $message ) {
		// TODO
	}
}

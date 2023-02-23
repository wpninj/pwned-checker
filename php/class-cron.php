<?php
/**
 * Notification class.
 *
 * Notify handler.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

use PwnedChecker\Interfaces\Initable;

/**
 * Notification class
 */
class Cron implements Initable {
	/**
	 * Cron hook name.
	 */
	const CRON_NAME = 'pwned_checker_cron';

	/**
	 * Cron init.
	 */
	public function init() {
		add_filter( 'cron_schedules', array( $this, 'add_wp_cron_schedule' ) );

		if ( ! wp_next_scheduled( self::CRON_NAME ) ) {
			wp_schedule_event( time(), 'every_2ten_minutes', self::CRON_NAME );
		}

		add_action( self::CRON_NAME, array( $this, 'run' ) );
	}

	/**
	 * Define custom cron schedule.
	 *
	 * @param array $schedules Schedule list array.
	 * @return array
	 */
	public function add_wp_cron_schedule( $schedules ) {
		$schedules['every_2ten_minutes'] = array(
			'interval' => 1200, // in seconds
			'display'  => __( 'Every 20 minutes', 'pwned-checker' ),
		);

		return $schedules;
	}

	/**
	 * Run cron.
	 */
	public function run() {
		$option = get_option( Settings::OPTION_NAME );
		if ( empty( $option['email_list'] ) ) {
			return;
		}

		$emails       = explode( ',', $option['email_list'] );
		$api          = new Api();
		$notification = new Notification();

		foreach ( $emails as $email ) {
			if ( is_email( $email ) ) {
				$breaches = $api->get_breaches( $email );
				if ( ! empty( $breaches ) ) {
					$breach_obj = json_decode( $breaches, true );
					if ( 401 == $breach_obj['statusCode'] ) {
						Utils::log( $breach_obj['message'] );
						return;
					}

					$message = wp_json_encode( $breach_obj );
					$notification->send( $message );
				}
			}
		}
	}
}

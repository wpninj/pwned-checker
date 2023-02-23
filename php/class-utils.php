<?php
/**
 * Holds common utility functions.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Class Utils
 *
 * @package PwnedChecker
 */
class Utils {

	/**
	 * All facets cache key
	 *
	 * @var string
	 */
	const ALL_FACETS_CACHE_KEY = 'all_facets';

	/**
	 * Get the instance of the this plugin.
	 *
	 * @return \PwnedChecker\Plugin
	 */
	public static function plugin() {
		return get_plugin_instance();
	}

	/**
	 * Get the instance of the cache API.
	 *
	 * @return \PwnedChecker\Cache
	 */
	public static function cache() {
		static $cache;

		if ( ! isset( $cache ) ) {
			// WP core will use the persistent object cache if available.
			$cache = new Cache_Transient();
		}

		return $cache;
	}

	/**
	 * Check whether a class implements an interface. If not, throw exception.
	 *
	 * @param string|object $class_being_checked Class being checked.
	 * @param string        $target_interface    The interface to check against.
	 *
	 * @throws \RuntimeException Throws exception if condition passes.
	 */
	public static function throw_if_not_of_type( $class_being_checked, $target_interface ) {
		if ( is_object( $class_being_checked ) && ! ( $class_being_checked instanceof $target_interface ) ) {
			throw new \RuntimeException( get_class( $class_being_checked ) . ' must implement ' . $target_interface );
		}

		if ( ! is_a( $class_being_checked, $target_interface, true ) ) {
			throw new \RuntimeException( $class_being_checked . ' must implement ' . $target_interface );
		}
	}

	/**
	 * Provides the results of a callable with caching.
	 *
	 * @param callable $callback A callback.
	 * @param string   $key Cache.
	 * @param int      $timeout The length of time in seconds after which to invalidate the cache.
	 *
	 * @return mixed Result of the callback.
	 */
	public static function with_cache( $callback, $key, $timeout = 0 ) {
		$result = self::cache()->get( $key );

		if ( ! is_array( $result ) || ! isset( $result['data'] ) ) {
			$result = array(
				'data' => call_user_func( $callback ),
			);

			if ( $result['data'] ) {
				self::cache()->set( $key, $result, $timeout );
			}
		}

		return $result['data'];
	}

	/**
	 * Log message.
	 *
	 * @param string $message Message to log.
	 */
	public static function log( $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $message );
		}
	}
}

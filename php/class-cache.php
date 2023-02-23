<?php
/**
 * Contract for the persistent cache APIs.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Interface Cache
 *
 * @package PwnedChecker
 */
abstract class Cache {

	/**
	 * Store cache value.
	 *
	 * @param string  $key Cache key.
	 * @param mixed   $data Cache value.
	 * @param integer $expire Max cache lifetime.
	 *
	 * @return bool If cache was stored.
	 */
	public function set( $key, $data, $expire = 0 ) {
		return wp_cache_set(
			$key,
			$data,
			$this->group(),
			$this->sanitize_expire( $expire ) // phpcs:ignore WordPressVIPMinimum.Performance.LowExpiryCacheTime.CacheTimeUndetermined
		);
	}

	/**
	 * Get cache value by key.
	 *
	 * @param string $key Cache key to look up.
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		return wp_cache_get( $key, $this->group() );
	}

	/**
	 * Delete cache value by key.
	 *
	 * @param string $key Cache key for the value to delete.
	 *
	 * @return bool If cache value was deleted.
	 */
	public function delete( $key ) {
		return wp_cache_delete( $key, $this->group() );
	}

	/**
	 * Ensure the cache time-to-live is long enough
	 * for performance.
	 *
	 * @param int $expire Cache expiry in seconds.
	 *
	 * @return int
	 */
	protected function sanitize_expire( $expire ) {
		$expire = absint( $expire );

		// Ensure we cache for 5 minutes or more.
		if ( $expire ) {
			return max( 300, $expire );
		}

		return $expire;
	}

	/**
	 * Get the shared cache group.
	 *
	 * @return string
	 */
	protected function group() {
		return __CLASS__;
	}

}

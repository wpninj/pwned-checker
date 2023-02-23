<?php
/**
 * Object cache API using transients.
 *
 * WP core will use the persistant object cache
 * if available instead of writing to the database.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Cache Transient class.
 */
class Cache_Transient extends Cache {

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
		return set_site_transient(
			$this->key( $key ),
			$data,
			$this->sanitize_expire( $expire )
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
		return get_site_transient( $this->key( $key ) );
	}

	/**
	 * Delete cache value by key.
	 *
	 * @param string $key Cache key for the value to delete.
	 *
	 * @return bool If cache value was deleted.
	 */
	public function delete( $key ) {
		return delete_site_transient( $this->key( $key ) );
	}

	/**
	 * Cache key that includes the group to avoid
	 * conflicts with other plugins.
	 *
	 * @param string $key Cache key to format.
	 *
	 * @return string
	 */
	protected function key( $key ) {
		$key_with_group = sprintf(
			'%s--%s__%s',
			$this->group(),
			substr( $key, 0, 20 ), // Add part of it for debugging purposes.
			md5( $key )
		);

		return substr( $key_with_group, 0, 172 );
	}

}

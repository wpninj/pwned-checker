<?php
/**
 * Class Plugin_Base
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Class Plugin_Base
 *
 * @package PwnedChecker
 */
abstract class Plugin_Base {

	/**
	 * Cache key used for storing the
	 * parsed plugin header meta.
	 *
	 * @var string
	 */
	const CACHE_KEY_PLUGIN_META = 'plugin-meta';

	/**
	 * Absolute path to the current plugin with the meta header.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	public $dir_path;

	/**
	 * Plugin directory URL.
	 *
	 * @var string
	 */
	public $dir_url;

	/**
	 * Directory in plugin containing autoloaded classes.
	 *
	 * @var string
	 */
	protected $autoload_class_dir = 'php';

	/**
	 * Set the environment type for toggling logging.
	 *
	 * @var string
	 */
	protected $environment_type = 'production';

	/**
	 * Autoload matches cache.
	 *
	 * @var array
	 */
	protected $autoload_matches_cache = [];

	/**
	 * Plugin_Base constructor.
	 *
	 * @param string $file Absolute path to the main plugin file.
	 */
	public function __construct( $file ) {
		$this->file     = $file;
		$this->dir_path = dirname( $file );
		$this->slug     = basename( $this->dir_path );
		$this->dir_url  = content_url( str_replace( wp_normalize_path( WP_CONTENT_DIR ), '', wp_normalize_path( $this->dir_path ) ) );

		spl_autoload_register( [ $this, 'autoload' ] );
	}

	/**
	 * Get reflection object for this class.
	 *
	 * @return \ReflectionObject
	 */
	public function get_object_reflection() {
		static $reflection;
		if ( empty( $reflection ) ) {
			// @codeCoverageIgnoreStart
			$reflection = new \ReflectionObject( $this );
			// @codeCoverageIgnoreEnd
		}

		return $reflection;
	}

	/**
	 * Autoload for classes that are in the same namespace as $this.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $class Class name.
	 *
	 * @return void
	 */
	public function autoload( $class ) {
		if ( ! isset( $this->autoload_matches_cache[ $class ] ) ) {
			if ( ! preg_match( '/^(?P<namespace>.+)\\\\(?P<class>[^\\\\]+)$/', $class, $matches ) ) {
				$matches = false;
			}

			$this->autoload_matches_cache[ $class ] = $matches;
		} else {
			$matches = $this->autoload_matches_cache[ $class ];
		}

		if ( empty( $matches ) ) {
			return;
		}

		$namespace = $this->get_object_reflection()->getNamespaceName();

		if ( strpos( $matches['namespace'], $namespace ) === false ) {
			return;
		}

		$class_name = $matches['class'];
		$class_path = \trailingslashit( $this->dir_path );

		if ( $this->autoload_class_dir ) {
			$class_path .= \trailingslashit( $this->autoload_class_dir );

			$sub_path = str_replace( $namespace . '\\', '', $matches['namespace'] );
			if ( ! empty( $sub_path ) && 'PwnedChecker' !== $sub_path ) {
				$class_path .= str_replace( '\\-', '/', strtolower( preg_replace( '/(?<!^)([A-Z])/', '-\\1', $sub_path ) ) . '/' );
			}
		}

		$class_path .= sprintf( 'class-%s.php', strtolower( str_replace( '_', '-', $class_name ) ) );

		if ( is_readable( $class_path ) ) {
			require_once $class_path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}
	}

	/**
	 * Version of plugin_dir_url() which works for plugins installed in the plugins directory,
	 * and for plugins bundled with themes.
	 *
	 * @return array
	 * @throws Exception If the plugin is not located in the expected location.
	 */
	public function locate_plugin() {
		return [
			'dir_url'      => $this->dir_url,
			'dir_path'     => $this->dir_path,
			'dir_basename' => $this->slug,
		];
	}

	/**
	 * Get the public URL to the asset file.
	 *
	 * @param string $path_relative Path relative to this plugin directory root.
	 *
	 * @return string The URL to the asset.
	 */
	public function url_to( $path_relative ) {
		return sprintf( '%s/%s', $this->dir_url, ltrim( $path_relative, '/\\' ) );
	}

	/**
	 * Get the absolute path to the asset file.
	 *
	 * @param string $path_relative Path relative to this plugin directory root.
	 *
	 * @return string Absolute path to the file.
	 */
	public function path_to( $path_relative ) {
		// Ensures parent directory traversal does not happen.
		if ( false === strpos( $path_relative, '..' ) ) {
			return sprintf( '%s/%s', $this->dir_path, ltrim( $path_relative, '/\\' ) );
		} else {
			return '';
		}
	}

	/**
	 * Configure the current site environment type.
	 *
	 * @param string $environment_type Environment type such as local, develoment, staging or production.
	 */
	public function set_site_environment_type( $environment_type ) {
		$this->environment_type = $environment_type;
	}

	/**
	 * Call trigger_error() if not on VIP production.
	 *
	 * @param string $message Warning message.
	 * @param int    $code    Warning code.
	 */
	public function trigger_warning( $message, $code = \E_USER_WARNING ) {
		if ( ! $this->is_production() && $this->is_debug() ) {
			trigger_error( esc_html( get_class( $this ) . ': ' . $message ), $code ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		}
	}

	/**
	 * Return whether we're on WordPress.com VIP production.
	 *
	 * @return bool
	 */
	public function is_production() {
		return ( 'production' === $this->environment_type );
	}

	/**
	 * Is WP debug mode enabled.
	 *
	 * @return boolean
	 */
	public function is_debug() {
		return ( defined( '\WP_DEBUG' ) && \WP_DEBUG );
	}

	/**
	 * Is WP script debug mode enabled.
	 *
	 * @return boolean
	 */
	public function is_script_debug() {
		return ( defined( '\SCRIPT_DEBUG' ) && \SCRIPT_DEBUG );
	}

	/**
	 * Return the current version of the plugin.
	 *
	 * @return mixed
	 */
	public function version() {
		$meta = Utils::with_cache(
			function () {
				return get_file_data(
					$this->file,
					[
						'Version' => 'Version',
					]
				);
			},
			self::CACHE_KEY_PLUGIN_META
		);

		if ( isset( $meta['Version'] ) ) {
			return $meta['Version'];
		}

		return time();
	}

	/**
	 * Sync the plugin version with the asset version.
	 *
	 * @return string|int
	 */
	public function asset_version() {
		if ( $this->is_debug() || $this->is_script_debug() ) {
			return time();
		}

		return $this->version();
	}
}

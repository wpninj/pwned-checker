<?php
/**
 * Proxy class to easily check which module should be active.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

use PwnedChecker\Interfaces\Initable;

/**
 * Module class.
 */
final class Module {

	/**
	 * Class name of the module being proxied.
	 *
	 * @var string
	 */
	private $module_class;

	/**
	 * Module constructor.
	 *
	 * @param string $module_class The class that's being proxied as a Module. Must implement the Initable interface.
	 */
	public function __construct( $module_class ) {
		Utils::throw_if_not_of_type( $module_class, Initable::class );

		$this->module_class = $module_class;
	}

	/**
	 * Inits the module after it passes validation.
	 */
	public function init() {
		$instance = new $this->module_class();
		$instance->init();
	}

}

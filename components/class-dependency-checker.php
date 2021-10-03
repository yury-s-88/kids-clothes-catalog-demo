<?php

namespace components;

/**
 * Class Dependency_Checker
 * @package components
 */
class Dependency_Checker implements Component {

	const ACF_PLUGIN_ID = 'advanced-custom-fields/acf.php';

	public function __construct() {}

	/**
	 * Register handlers for hooks
	 */
	public function register() : void {
		add_action( 'admin_init', array( $this, 'check_acf' ) );
	}

	/**
	 * Callback for admin init hook
	 * Add notice to admin area in case ACF plugin is not active
	 */
	public function check_acf() : void {
		if ( ! self::check_acf_installed() ) {
			$message = 'ACF plugin is not installed/activated. Some features will be disabled.';
			( Admin_Notice_Manager::get_instance() )->add_admin_notice( 'demo-not-loaded', $message, 'error' );
		}
	}

	/**
	 * Function checks if acf plugin is installed and active.
	 * @access private
	 * @return bool
	 */
	public static function check_acf_installed() : bool {
		return is_plugin_active( self::ACF_PLUGIN_ID );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister() : void {
		remove_action( 'admin_init', array( $this, 'check_acf' ) );
	}
}

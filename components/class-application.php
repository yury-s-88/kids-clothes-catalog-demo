<?php
namespace components;

/**
 * Class Application
 * @package components
 *
 * @property-read Custom_Types $Custom_Types
 * @property-read Demo_Data $Demo_Data
 * @property-read Dependency_Checker $Dependency_Checker
 * @property-read Theme_Installer $Theme_Installer
 */
class Application {

	const VERSION = '1.0.0';

	private $core_components = array(
		'Custom_Types',
		'Demo_Data',
		'Dependency_Checker',
		'Theme_Installer',
	);

	private $components_instances = array();

	public function __construct() {
		foreach ( $this->core_components as $component_class ) {
			$class_name         = '\components\\' . $component_class;
			$component_instance = new $class_name();

			$this->components_instances[ $component_class ] = $component_instance;
		}
		$this->register_assets();
	}

	/**
	 * Register global CSS and JS assets
	 */
	public function register_assets() : void {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_site_assets' ) );
	}

	/**
	 * Global assets for admin area
	 */
	public function register_admin_assets() : void {
		wp_enqueue_script( 'app-lib', get_template_directory_uri() . '/components/assets/lib.js', array(), self::VERSION, true );
		wp_enqueue_style( 'app-general', get_template_directory_uri() . '/components/assets/general.css', array(), self::VERSION );
	}

	/**
	 * Global assets for site
	 */
	public function register_site_assets() : void {
		wp_enqueue_script( 'app-lib', get_template_directory_uri() . '/components/assets/lib.js', array(), self::VERSION, true );
		wp_enqueue_style( 'app-general', get_template_directory_uri() . '/components/assets/general.css', array(), self::VERSION );
	}

	/**
	 * Remove component from a list of core components that are intended to be launched automatically.
	 * Must be invoked before 'run' action
	 * @param string $component Component class name
	 * @return bool - true if component was found
	 */
	public function remove_component( string $component ) : bool {
		if ( isset( $this->components_instances[ $component ] ) ) {
			unset( $this->components_instances[ $component ] );
			return true;
		}
		return false;
	}

	/**
	 * Register all components
	 */
	public function run() : void {
		foreach ( $this->components_instances as $component ) {
			$component->register();
		}
	}

	/**
	 * Allows to access core components as an object property
	 * @param $name
	 * @return mixed|null
	 */
	public function __get( $name ) {
		if ( isset( $this->components_instances[ $name ] ) ) {
			return $this->components_instances[ $name ];
		}
		return null;
	}
}

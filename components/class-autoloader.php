<?php /** @noinspection PhpIncludeInspection */

/**
 * Class Autoloader
 *
 */
class Autoloader {

	/**
	 * Registered namespaces/prefixes
	 *
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * Adds the classloader to the SPL autoloader stack
	 *
	 * @param bool $prepend Whether to prepend to the stack
	 *
	 * @return bool Returns true on success or false on failure
	 */
	public function register( $prepend = false ) {
		return spl_autoload_register( array( $this, 'load_class' ), true, $prepend );
	}

	/**
	 * Removes the classloader from the SPL autoloader stack
	 *
	 * @return bool Returns true on success or false on failure
	 */
	public function unregister() {
		return spl_autoload_unregister( array( $this, 'load_class' ) );
	}

	/**
	 * Registers a namespace
	 *
	 * @param string $namespace
	 * @param string $path
	 * @param bool $prepend
	 *
	 * @return null
	 */
	public function add_namespace( string $namespace, string $path, $prepend = false ) {
		// normalize namespace
		$namespace = trim( $namespace, '\\' );
		// normalize path
		$path = rtrim( $path, DIRECTORY_SEPARATOR );
		// add namespace
		if ( $prepend ) {
			array_unshift( $this->namespaces, array( $namespace, $path ) );
		} else {
			array_push( $this->namespaces, array( $namespace, $path ) );
		}
		return true;
	}

	/**
	 * Returns the registered namespaces
	 *
	 * @return array
	 */
	public function get_namespaces() {
		return $this->namespaces;
	}

	/**
	 * Tries to resolve the given class from the registered namespaces
	 *
	 * @param string $class
	 *
	 * @return mixed
	 */
	public function load_class( string $class ) {

		// check all registered namespaces
		foreach ( $this->namespaces as $namespace ) {
			list( $prefix, $path ) = $namespace;
			// find a matching prefix
			if ( 0 === strpos( $class, $prefix ) ) {
				$class_name      = substr( $class, strlen( $prefix ) + 1 );
				$class_name_base = str_replace( '_', '-', strtolower( $class_name ) );
				$class_file_name = 'class-' . $class_name_base;

				// require the file if it exists
				$class_file = $path . str_replace( '\\', DIRECTORY_SEPARATOR, $class_file_name ) . '.php';
				if ( is_readable( $class_file ) ) {
					require $class_file;
					return true;
				}

				$interface_file_name = 'interface-' . $class_name_base;
				$interface_file      = $path . str_replace( '\\', DIRECTORY_SEPARATOR, $interface_file_name ) . '.php';
				if ( is_readable( $interface_file ) ) {
					require $interface_file;
					return true;
				}
			}
		}
		// no file was found
		return false;
	}
}

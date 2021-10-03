<?php
namespace components;

/**
 * Short description
 *
 */

/**
 * Class AdminNoticeManager
 */
class Admin_Notice_Manager {

	/**
	 * @var array
	 * @access private
	 */
	private $notices;

	/**
	 * @var self
	 * @access private
	 */
	private static $instance;

	/**
	 * @var bool
	 * @access private
	 */
	private $is_initialized;

	/**
	 * Admin_Notice_Manager constructor.
	 * Cannot be called directly
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Allows to get single instance of class
	 * @return static
	 */
	public static function get_instance() : self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Object initialization
	 */
	public function init() : void {
		if ( $this->is_initialized ) {
			return;
		}
		add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
		$this->is_initialized = true;
	}

	/**
	 * Add new notice to stack
	 * @param string $id
	 * @param string $message
	 * @param string $type - 'error'|'success'|'warning'|'info'
	 * @param bool|false $is_dismissible
	 */
	public function add_admin_notice( string $id, string $message, string $type = 'default', bool $is_dismissible = false ) : void {
		$this->notices[ $id ] = array_slice( func_get_args(), 1 );
	}

	/**
	 * Remove a notice from stack using key
	 * @param string $id
	 * @return bool
	 */
	public function remove_admin_notice( string $id ) : bool {
		if ( isset( $this->notices[ $id ] ) ) {
			unset( $this->notices[ $id ] );
			return true;
		}
		return false;
	}

	/**
	 * Callback for 'admin_notices' hook
	 */
	public function show_admin_notices() : void {
		foreach ( $this->notices as $notice_args ) {
			$html = call_user_func_array( array( $this, 'get_admin_notice' ), $notice_args );
			echo wp_kses_post( $html );
		}
	}

	/**
	 * Return html for a single notice
	 * @param string $message
	 * @param string $type - 'error'|'success'|'warning'|'info'
	 * @param bool $is_dismissible
	 * @return string
	 */
	private function get_admin_notice( string $message, string $type = 'default', bool $is_dismissible = false ) : string {
		$notice_class      = in_array( $type, array( 'error', 'success', 'warning', 'info' ), true ) ? "notice-{$type}" : 'notice-default';
		$dismissible_class = $is_dismissible ? 'is-dismissible' : '';
		ob_start();
		?>
		<div id="message" class="notice <?php echo esc_attr( $notice_class ); ?> <?php echo esc_attr( $dismissible_class ); ?>">
			<p>
				<?php echo wp_kses_post( $message ); ?>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}
}

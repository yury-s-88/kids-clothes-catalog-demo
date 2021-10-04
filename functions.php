<?php

use components\Application;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );

	// Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	// Get the theme data
	$the_theme = wp_get_theme();
	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $the_theme->get( 'Version' ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get( 'Version' ), true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), $the_theme->get( 'Version' ) );
	wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/custom.js', array( 'app-lib' ), $the_theme->get( 'Version' ), true );
}

function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

// --------------------
define( 'DISALLOW_FILE_EDIT', true );

add_action( 'wp_print_footer_scripts', 'ajaxurl_footer_script', 10, 2 );
function ajaxurl_footer_script() {
	?>
	<script>window.ajaxurl = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"</script>
	<?php
}

require_once get_stylesheet_directory() . '/components/class-autoloader.php';
$autoloader = new Autoloader();
$autoloader->add_namespace( 'components', get_stylesheet_directory() . '/components/' );
$autoloader->register();

$app = new Application();
$app->demo_data->set_always_show( true );
if ( defined( 'NO_DEMO_DATA' ) && NO_DEMO_DATA ) {
	$app->remove_component( 'demo_data' );
}
$app->run();

/**
 * Change default 10 per page without touching settings
 */
add_action( 'pre_get_posts', 'clothes_per_page_amount' );
function clothes_per_page_amount( $query ) {
	if ( ( $query->is_post_type_archive( 'clothes' ) || $query->is_tax( 'clothes-type' ) ) && ! is_admin() && $query->is_main_query() ) {
		$query->set( 'posts_per_page', '12' );
	}
	return $query;
}

add_action( 'wp_ajax_cci_form', 'cci_form_handler' );
function cci_form_handler() {
	if ( ! wp_verify_nonce( $_POST['cci_form_nonce'], 'cci_form' ) ) {
		wp_send_json_error( array( 'message' => 'Session expired' ) );
	}

	$title       = $_POST['cci_clothes_name'] ?? '';
	$description = $_POST['cci_clothes_description'] ?? '';
	$size        = $_POST['cci_clothes_size'] ?? '';
	$color       = $_POST['cci_clothes_color'] ?? '';
	$sex         = $_POST['cci_clothes_sex'] ?? '';
	$type        = $_POST['cci_clothes_type'] ?? '';

	if (
		! $title ||
		! $description ||
		! $size ||
		! $color ||
		! $sex ||
		! $type ||
		! is_array( $type )
	) {
		wp_send_json_error( array( 'message' => 'Required parameter(s) was not passed' ) );
	}
	$terms_ids = array_map( 'intval', $type );

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'clothes',
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
		)
	);

	update_post_meta( $post_id, 'size', $size );
	update_post_meta( $post_id, 'color', $color );
	update_post_meta( $post_id, 'sex', $sex );

	if ( isset( $_FILES['cci_clothes_image'] ) ) {
		$attachment_id = media_handle_upload( 'cci_clothes_image', $post_id );
		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	wp_set_post_terms( $post_id, $terms_ids, 'clothes-type' );

	wp_send_json_success();
}

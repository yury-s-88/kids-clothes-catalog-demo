<?php

namespace components;

use WP_Error;

class Demo_Data implements Component {

	const DEMO_DATA_PAGE_SLUG = 'demo-data-page';

	const VERSION = '1.0.0';

	const DEMO_POST_LOADED_OPTION_NAME  = 'demo_posts_created';
	const DEMO_TERMS_LOADED_OPTION_NAME = 'demo_taxonomies_created';

	public function __construct() {
	}

	/**
	 * Register handlers for hooks
	 */
	public function register() : void {
		if ( ! self::check_is_demo_loaded() ) {
			add_action( 'admin_menu', array( $this, 'register_demo_data_page' ) );
			add_action( 'current_screen', array( $this, 'schedule_demo_notice' ) );
			add_action( 'wp_ajax_generate_demo_clothes', array( $this, 'generate_demo_clothes' ) );
			add_action( 'wp_ajax_generate_demo_clothes_types', array( $this, 'generate_demo_clothes_types' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		}
	}

	/**
	 * Register self script(s) and style(s)
	 */
	public function register_assets() : void {
		$base_url = get_template_directory_uri() . '/components/assets/';
		wp_enqueue_style( 'demo-data', $base_url . 'demo-data.css', array(), self::VERSION );
		wp_enqueue_script( 'demo-data', $base_url . 'demo-data.js', array( 'app-lib' ), self::VERSION, true );
	}

	/**
	 * Returns true if demo data has already been created
	 * @return bool
	 */
	public static function check_is_demo_loaded(): bool {
		return get_option( self::DEMO_POST_LOADED_OPTION_NAME ) && get_option( self::DEMO_TERMS_LOADED_OPTION_NAME );
	}

	/**
	 * Adds page to admin area. The page is hidden from menu and accessible by link in admin notice.
	 */
	public function register_demo_data_page() : void {
		add_submenu_page(
			null, // don't show in menu
			'Demo data',
			'',
			'manage_options',
			self::DEMO_DATA_PAGE_SLUG,
			array( $this, 'demo_page_callback' )
		);
	}

	/**
	 * Content for page with demo data generation settings
	 */
	public function demo_page_callback() : void {
		$default_sizes  = array( 'XS', 'S', 'M', 'L', 'XL', 'XXL' );
		$default_colors = array( 'Red', 'Green', 'Blue', 'Cyan', 'Yellow', 'Magenta', 'White', 'Black' );
		$default_sexes  = array( 'Male', 'Female' );

		$default_taxonomies = array( 'T-shirt', 'Pants', 'Jacket', 'Hat', 'New', 'Accessories', 'Coats', 'Dresses', 'Jeans', 'Jumpers', 'Petite', 'Shoes', 'Swimwear', 'Tall', 'Tops', 'Trousers' );

		$step_1_done = get_option( 'demo_posts_created' );
		$step_2_done = get_option( 'demo_taxonomies_created' );
		switch ( true ) {
			case $step_1_done && $step_2_done:
				$step = 3;
				break;
			case $step_1_done && ! $step_2_done:
				$step = 2;
				break;
			default:
				$step = 1;
		}
		?>
		<main class="main">
			<?php wp_nonce_field(); ?>
			<h1>Demo data</h1>

			<section class="demo-data-step-1 <?php echo ( 1 === $step ) ? '' : 'hidden'; ?>">
				<h2>Step 1: Create demo posts</h2>
				<form method="post" id="generate_demo_clothes">
					<input type="hidden" name="action" value="generate_demo_clothes">
					<div class="demo-form-row">
						<label class="label" for="clothes_amount">Number of clothes items to create</label>
						<input type="number" min="0" id="clothes_amount" name="clothes_amount" value="10">
					</div>
					<div class="demo-form-row">
						<div class="label">Create with random thumbnails?</div>
						<label>
							<input type="radio" id="clothes_thumbnails_y" name="clothes_thumbnails" value="1" checked> Yes
						</label>
						<label>
							<input type="radio" id="clothes_thumbnails_n" name="clothes_thumbnails" value="0"> No
						</label>
					</div>
					<div class="demo-form-row">
						<?php if ( Dependency_Checker::check_acf_installed() ) : ?>
						<div class="label">
							Values for ACF fields <br>
							<i>Enter each option on a new line.</i>
						</div>
						<div class="values_container">
							<div class="values_col">
								<label class="label" for="values_size">
									Sizes
								</label>
								<textarea id="values_size" name="values_size"><?php echo esc_html( join( "\r\n", $default_sizes ) ); ?></textarea>

								<div class="label">Use values</div>
								<label>
									<input type="radio" id="use_random_sizes_random_y" name="use_random_sizes" value="1" checked> Random
								</label>
								<label>
									<input type="radio" id="use_random_sizes_random_n" name="use_random_sizes" value="0"> Loop
								</label>
							</div>
							<div class="values_col">
								<label class="label" for="values_color">
									Color
								</label>
								<textarea id="values_color" name="values_color"><?php echo esc_html( join( "\r\n", $default_colors ) ); ?></textarea>

								<div class="label">Use values</div>
								<label>
									<input type="radio" id="colors_usage_random_y" name="colors_usage" value="1" checked> Random
								</label>
								<label>
									<input type="radio" id="colors_usage_random_n" name="colors_usage" value="0"> Loop
								</label>
							</div>
							<div class="values_col">
								<label class="label" for="values_sex">
									Sex
								</label>
								<textarea id="values_sex" name="values_sex"><?php echo esc_html( join( "\r\n", $default_sexes ) ); ?></textarea>

								<div class="label">Use values</div>
								<label>
									<input type="radio" id="sexes_usage_random_y" name="sexes_usage" checked> Random
								</label>
								<label>
									<input type="radio" id="sexes_usage_random_n" name="sexes_usage"> Loop
								</label>
							</div>
							<?php else : ?>
								<div class="label">
									ACF plugin is not activated. Generation of meta fields is disabled
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="demo-form-row">
						<div class="button button-primary" id="demo_generate_clothes">Generate</div>
					</div>
				</form>
			</section>

			<section class="demo-data-step-2 <?php echo ( 2 === $step ) ? '' : 'hidden'; ?>">
				<h2>Step 2: Create and assign taxonomies</h2>
				<form method="post" id="generate_demo_clothes_types">
					<input type="hidden" name="action" value="generate_demo_clothes_types">
					<div class="demo-form-row">
						<div class="taxonomies_container">
							<label class="label" for="values_taxonomy">
								List of taxonomies
							</label>
							<textarea id="values_taxonomy" name="values_taxonomy"><?php echo esc_html( join( "\r\n", $default_taxonomies ) ); ?></textarea>
						</div>

					</div>

					<div class="demo-form-row">
						<?php if ( Dependency_Checker::check_acf_installed() ) : ?>
							<div class="label">Create with random thumbnails?</div>
							<label>
								<input type="radio" id="clothes_types_thumbnails_y" name="clothes_types_thumbnails" value="1" checked> Yes
							</label>
							<label>
								<input type="radio" id="clothes_types_thumbnails_n" name="clothes_types_thumbnails" value="0"> No
							</label>
						<?php else : ?>
							<div class="label">
								ACF plugin is not activated. Generation of meta fields is disabled
							</div>
						<?php endif; ?>
					</div>

					<div class="demo-form-row">
						<div class="label">Assign taxonomies to posts</div>
						<div class="demo-assign-container">
							<div class="demo-assign-container_label-col">
								<label for="clothes_types_min_amount">Min per post</label> (<i>0 to allow skip</i>)
							</div>
							<div class="demo-assign-container_value-col">
								<input type="number" id="clothes_types_min_amount" name="clothes_types_min_amount" min="0" value="1">
							</div>

						</div>
						<div class="demo-assign-container">
							<div class="demo-assign-container_label-col">
								<label for="clothes_types_max_amount">Max per post</label> (<i>0 to deny assignment</i>)
							</div>
							<div class="demo-assign-container_value-col">
								<input type="number" id="clothes_types_max_amount" name="clothes_types_max_amount" min="0" value="2">
							</div>
						</div>
					</div>

					<div class="demo-form-row">
						<div class="button button-primary" id="demo_generate_clothes_types">Generate</div>
					</div>
				</form>
			</section>

			<section class="demo-data-step-3 <?php echo ( 3 === $step ) ? '' : 'hidden'; ?>">
				<h2>Demo data created successfully</h2>
				<p>
					<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=clothes' ) ); ?>">Return to clothes posts</a><br>
					<a href="<?php echo esc_url( get_site_url() ); ?>">Go to home page</a>
				</p>
			</section>

		</main>
		<?php
	}

	/**
	 * Current screen cannot be checked before 'current screen' action.
	 * So, check screen once it is possible and add notice if necessary.
	 */
	public function schedule_demo_notice() : void {
		if ( self::check_demo_screen() ) {
			self::demo_notice();
		}
	}

	/**
	 * Returns true if current screen ID in the list of screens where notice can be displayed.
	 * @return bool
	 */
	public function check_demo_screen(): bool {
		$screen = get_current_screen();

		return in_array( $screen->id, array( 'edit-clothes', 'clothes', 'edit-clothes-type' ), true );
	}

	/**
	 * Callback for 'current_screen'
	 * Check cannot be called in 'init' method so use this method to schedule the check
	 */
	public function demo_notice(): void {
		$url = get_admin_url() . '?page=' . self::DEMO_DATA_PAGE_SLUG;

		$message = "You may create demo data for cloth. <a href='{$url}'>Click here</a> to do it";
		( Admin_Notice_Manager::get_instance() )->add_admin_notice( 'acf-error', $message, 'info' );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister() : void {
		remove_action( 'admin_menu', array( $this, 'register_demo_data_page' ) );
		remove_action( 'current_screen', array( $this, 'schedule_demo_notice' ) );
		remove_action( 'wp_ajax_generate_demo_clothes', array( $this, 'generate_demo_clothes' ) );
		remove_action( 'wp_ajax_generate_demo_clothes_types', array( $this, 'generate_demo_clothes_types' ) );
		remove_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Callback for ajax handler
	 * Error response contains array element 'message'
	 */
	public function generate_demo_clothes() : void {
		if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
			wp_send_json_error( array( 'message' => 'session expired' ) );
		}

		$amount          = $_POST['clothes_amount'] ?? 0;
		$current_user_id = get_current_user_id();

		$errors = array();

		/** @noinspection SpellCheckingInspection */
		$description = <<<DESCRIPTION
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore 
magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo 
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla 
pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id 
est laborum.
DESCRIPTION;

		$sizes  = array();
		$colors = array();
		$sexes  = array();

		$sizes_values  = $_POST['values_size'] ?? false;
		$colors_values = $_POST['values_color'] ?? false;
		$sexes_values  = $_POST['values_sex'] ?? false;

		$use_random_sizes  = $_POST['use_random_sizes'] ?? false;
		$use_random_colors = $_POST['use_random_colors'] ?? false;
		$use_random_sexes  = $_POST['use_random_sexes'] ?? false;

		if ( $sizes_values ) {
			$sizes = explode( "\n", $sizes_values );
		}

		if ( $colors_values ) {
			$colors = explode( "\n", $colors_values );
		}

		if ( $sexes_values ) {
			$sexes = explode( "\n", $sexes_values );
		}

		for ( $i = 0; $i < $amount; $i++ ) {
			$number = $i + 1;
			$title  = "Clothes item {$number}";

			$clothes_args = array(
				'post_type'    => Custom_Types::CLOTHES_POST_TYPE,
				'post_title'   => $title,
				'post_content' => $description,
				'post_status'  => 'publish',
				'post_author'  => $current_user_id,
			);

			$post_id = wp_insert_post( $clothes_args );

			if ( is_wp_error( $post_id ) ) {
				$errors[] = $post_id->get_error_message();
				continue;
			}

			$thumbnails_requested = $_POST['clothes_thumbnails'] ?? false;
			if ( $thumbnails_requested ) {
				$image_url     = "https://robohash.org/{$number}?set=set2&size=360x360";
				$attachment_id = $this->get_remote_image( $image_url, "Thumb {$number}.png" );
				if ( is_wp_error( $attachment_id ) ) {
					$errors[] = 'Image download error: ' . $attachment_id->get_error_message();
				} else {
					set_post_thumbnail( $post_id, $attachment_id );
				}
			}

			if ( $sizes ) {
				if ( $use_random_sizes ) {
					$tmp_index = wp_rand( 0, count( $sizes ) - 1 );
					$size      = $sizes[ $tmp_index ];
				} else {
					$size = current( $sizes );
					if ( ! next( $sizes ) ) {
						reset( $sizes );
					}
				}
				update_field( 'size', $size, $post_id );
			}

			if ( $colors ) {
				if ( $use_random_colors ) {
					$tmp_index = wp_rand( 0, count( $colors ) - 1 );
					$color     = $colors[ $tmp_index ];
				} else {
					$color = current( $colors );
					if ( ! next( $colors ) ) {
						reset( $colors );
					}
				}
				update_field( 'color', $color, $post_id );
			}

			if ( $sexes ) {
				if ( $use_random_sexes ) {
					$tmp_index = wp_rand( 0, count( $sexes ) - 1 );
					$sex       = $sexes[ $tmp_index ];
				} else {
					$sex = current( $sexes );
					if ( ! next( $sexes ) ) {
						reset( $sexes );
					}
				}
				update_field( 'sex', $sex, $post_id );
			}
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'message' => join( "\r\n", $errors ) ) );
		}
		update_option( self::DEMO_POST_LOADED_OPTION_NAME, 1, true );
		wp_send_json_success( $_POST );
	}

	/**
	 * Callback for ajax handler
	 * Error response contains array element 'message'
	 */
	public function generate_demo_clothes_types() : void {
		if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
			wp_send_json_error( array( 'message' => 'session expired' ) );
		}

		$errors = array();

		$taxonomies_values    = $_POST['values_taxonomy'] ?? false;
		$thumbnails_requested = $_POST['clothes_types_thumbnails'] ?? false;

		if ( ! $taxonomies_values ) {
			wp_send_json_error( array( 'message' => 'nothing to create' ) );
		}
		$terms = explode( "\n", $taxonomies_values );

		$terms_ids = array();

		$max_terms_per_post = $_POST['clothes_types_max_amount'] ?? 0;
		$min_terms_per_post = $_POST['clothes_types_min_amount'] ?? 0;
		if ( $min_terms_per_post > $max_terms_per_post ) {
			$min_terms_per_post = $max_terms_per_post;
		}

		$tax_name = Custom_Types::CLOTH_TAXONOMY_NAME;

		/** @noinspection SpellCheckingInspection */
		$description = <<<DESCRIPTION
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore 
magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo 
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla 
pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id 
est laborum.
DESCRIPTION;

		foreach ( $terms as $k => $term_name ) {
			$number = $k + 1;

			if ( term_exists( $term_name, Custom_Types::CLOTH_TAXONOMY_NAME ) ) {
				continue;
			}

			$term = wp_insert_term(
				$term_name,
				Custom_Types::CLOTH_TAXONOMY_NAME,
				array(
					'description' => $description,
				)
			);

			if ( is_wp_error( $term ) ) {
				$errors[] = $term->get_error_message();
				continue;
			}

			if ( $thumbnails_requested ) {
				$image_url     = "https://robohash.org/{$number}?set=set3&size=360x360";
				$attachment_id = $this->get_remote_image( $image_url, "Thumb {$number}.png" );

				if ( is_wp_error( $attachment_id ) ) {
					$errors[] = $attachment_id->get_error_message();
				} else {
					update_field( 'image', $attachment_id, $tax_name . '_' . $term['term_id'] );
					$terms_ids[] = $term['term_id'];
				}
			}

			if ( ! $max_terms_per_post ) {
				continue;
			}
		}

		$clothes_posts_ids = get_posts(
			array(
				'post_type'      => Custom_Types::CLOTHES_POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		$terms_per_post = wp_rand( $min_terms_per_post, $max_terms_per_post );
		foreach ( $clothes_posts_ids as $posts_id ) {
			shuffle( $terms_ids );
			$terms_ids_to_assign = array_slice( $terms_ids, 0, $terms_per_post );
			wp_set_post_terms( $posts_id, $terms_ids_to_assign, Custom_Types::CLOTH_TAXONOMY_NAME );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'message' => join( "\r\n", $errors ) ) );
		}
		update_option( self::DEMO_TERMS_LOADED_OPTION_NAME, 1, true );
		wp_send_json_success( $_POST );
	}

	/**
	 * Download image from remote url. Returns ID of the created media attachment
	 * @param string $image_url - url to download image from
	 * @param string $image_name - name of image to save
	 *
	 * @return int|WP_Error - attachment ID
	 */
	public function get_remote_image( string $image_url, string $image_name ) {
		// Ensure url is valid.
		$image_url = esc_url_raw( $image_url );

		$file_array         = array();
		$file_array['name'] = $image_name;

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $image_url );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return new WP_Error( 'demo_data_error', 'Invalid image', array( 'status' => 400 ) );
		}

		// Do the validation and storage stuff.
		$attachment_id = media_handle_sideload( $file_array, array( 'test_form' => false ), current_time( 'Y/m' ) );

		if ( isset( $file['error'] ) ) {
			return new WP_Error( 'demo_data_error', 'Invalid image', array( 'status' => 400 ) );
		}

		return $attachment_id;
	}
}

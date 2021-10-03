<?php
namespace components;

use WP_Error;

/**
 * Class Custom_Types
 * @package components
 */
class Custom_Types implements Component {

	const CLOTHES_POST_TYPE = 'clothes';

	const CLOTH_TAXONOMY_NAME = 'clothes-type';

	public function __construct() {}

	/**
	 * Add custom post type and taxonomy
	 */
	public function register() : void {
		add_action( 'init', array( $this, 'register_clothes_type_taxonomy' ) );
		add_action( 'init', array( $this, 'register_clothes_post_type' ) );
	}

	/**
	 * Callback for 'init' hook
	 * Registers custom post type
	 */
	public function register_clothes_post_type() : void {
		$res = register_post_type(
			self::CLOTHES_POST_TYPE,
			array(
				'labels'             => array(
					'name'               => 'Clothes',
					'singular_name'      => 'Clothes',
					'add_new'            => 'Add new',
					'add_new_item'       => 'Add new clothes',
					'edit_item'          => 'Edit clothes',
					'new_item'           => 'New Clothes',
					'view_item'          => 'View clothes',
					'search_items'       => 'Search clothes',
					'not_found'          => 'No clothes found',
					'not_found_in_trash' => 'No clothes found in trash.',
					'parent_item_colon'  => '',
					'menu_name'          => 'Clothes',

				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => true,
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'thumbnail', 'editor', 'author' ),
			)
		);

		if ( is_a( $res, WP_Error::class ) && is_admin() ) {
			( Admin_Notice_Manager::get_instance() )->add_admin_notice( 'register_cloth_post_type_error', $res->get_error_message(), 'error' );
		}
	}

	/**
	 * Callback for 'init' hook
	 * Registers custom taxonomy for 'clothes' post type
	 */
	public function register_clothes_type_taxonomy() : void {

		register_taxonomy(
			self::CLOTH_TAXONOMY_NAME,
			array( self::CLOTHES_POST_TYPE ),
			array(
				'hierarchical' => false,
				'labels'       => array(
					'name'              => 'Clothes types',
					'singular_name'     => 'Clothes type',
					'search_items'      => 'Search Clothes types',
					'all_items'         => 'All Clothes types',
					'parent_item'       => 'Parent Clothes type',
					'parent_item_colon' => 'Parent Clothes type:',
					'edit_item'         => 'Edit Clothes type',
					'update_item'       => 'Update Clothes type',
					'add_new_item'      => 'Add New Clothes type',
					'new_item_name'     => 'New Clothes type Name',
					'menu_name'         => 'Clothes type',
				),
				'show_ui'      => true,
				'query_var'    => true,
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function unregister() : void {
		remove_action( 'init', array( $this, 'register_clothes_type_taxonomy' ) );
		remove_action( 'init', array( $this, 'register_clothes_post_type' ) );
	}
}

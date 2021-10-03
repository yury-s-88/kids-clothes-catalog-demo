<?php
namespace components;

/**
 * Class Theme_Installer
 * @package components
 */
class Theme_Installer implements Component {

	const POST_TYPE_GROUP_NAME = 'Clothes fields';

	const TAXONOMY_GROUP_NAME = 'Clothes type fields';

	const POST_TYPE_GROUP_FIELDS = array(
		array(
			'label' => 'Size',
			'type'  => 'text',
		),
		array(
			'label' => 'Color',
			'type'  => 'text',
		),
		array(
			'label' => 'Sex',
			'type'  => 'text',
		),
	);

	public function __construct() {}

	/**
	 * Register handlers for hooks
	 */
	public function register() : void {
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'admin_init', array( $this, 'check_acf_fields_notice' ), 11 );
	}

	/**
	 * Register additional handlers for hooks on theme activation action
	 */
	public function after_switch_theme() : void {
		add_action( 'admin_init', array( $this, 'register_post_type_acf_fields' ) );
		add_action( 'admin_init', array( $this, 'register_taxonomy_acf_fields' ) );
	}

	/**
	 * Add admin notice in case acf group(s) and/or fields are missed
	 */
	public function check_acf_fields_notice() : void {
		if ( ! $this->check_acf_fields_created() ) {
			$message = 'ACF fields was not created properly';
			( Admin_Notice_Manager::get_instance() )->add_admin_notice( 'acf_fields_create_error', $message );
		}
	}

	/**
	 * Returns true if all ACF groups and fields were created
	 * @return bool
	 */
	public function check_acf_fields_created() : bool {
		$field_group = get_page_by_title( self::POST_TYPE_GROUP_NAME, OBJECT, 'acf-field-group' );
		if ( ! $field_group ) {
			return false;
		}
		foreach ( self::POST_TYPE_GROUP_FIELDS as $group_field_args ) {
			$field_label = $group_field_args['label'];
			$field_name  = sanitize_title( $field_label );
			if ( ! acf_get_field( $field_name ) ) {
				return false;
			}
		}

		$field_group = get_page_by_title( self::TAXONOMY_GROUP_NAME, OBJECT, 'acf-field-group' );
		if ( ! $field_group ) {
			return false;
		}

		if ( ! acf_get_field( 'image' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function register_post_type_acf_fields() : bool {

		if ( ! Dependency_Checker::check_acf_installed() ) {
			return false;
		}

		$group_id = $this->create_field_group( self::POST_TYPE_GROUP_NAME );
		if ( ! $group_id ) {
			return false;
		}

		// --------------------------
		foreach ( self::POST_TYPE_GROUP_FIELDS as $group_field_args ) {
			$field_label = $group_field_args['label'];
			$field_name  = sanitize_title( $field_label );

			if ( acf_get_field( $field_name ) ) {
				continue;
			}

			acf_update_field(
				array(
					'label'             => $field_label,
					'key'               => uniqid( 'field_' ),
					'name'              => $field_name,
					'type'              => $group_field_args['type'],
					'prefix'            => '',
					'instructions'      => '',
					'required'          => false,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
					'parent'            => $group_id,
					'menu_order'        => 0,
				)
			);
		}
		return true;
	}

	/**
	 * Create ACF fields group and fields for taxonomy
	 * @return bool
	 */
	public function register_taxonomy_acf_fields() :bool {

		if ( ! Dependency_Checker::check_acf_installed() ) {
			return false;
		}

		$group_id = $this->create_field_group( self::TAXONOMY_GROUP_NAME );
		if ( ! $group_id ) {
			return false;
		}

		// ----------------------------------------------
		$field_label = 'Image';
		$field_name  = sanitize_title( $field_label );

		if ( ! acf_get_field( $field_name ) ) {
			acf_update_field(
				array(
					'label'             => $field_label,
					'key'               => uniqid( 'field_' ),
					'name'              => $field_name,
					'type'              => 'image',
					'instructions'      => '',
					'required'          => false,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'readonly'          => 0,
					'disabled'          => 0,
					'parent'            => $group_id,
				)
			);
		}
		return true;
	}

	/**
	 * Create ACF field group with a given name
	 * @param $name
	 * @return int
	 */
	private function create_field_group( $name ) : int {

		$field_group = get_page_by_title( $name, OBJECT, 'acf-field-group' );
		// --------------------------
		if ( $field_group ) {
			$group_id = $field_group->ID;
		} else {
			$field_group_args = array(
				'active'   => 'publish',
				'title'    => $name,
				'key'      => uniqid( 'group_' ),  // post slug in DB
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => Custom_Types::CLOTHES_POST_TYPE,
						),
					),
				),
			);
			$field_group_res  = acf_update_field_group( $field_group_args );
			$group_id         = $field_group_res['ID'] ?? 0;
		}
		return $group_id;
	}

	/**
	 * @inheritDoc
	 */
	public function unregister() : void {
		remove_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		remove_action( 'admin_init', array( $this, 'check_acf_fields_notice' ), 11 );
	}
}

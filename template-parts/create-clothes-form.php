<?php
$clothes_types = get_terms(
	array(
		'taxonomy' => 'clothes-type',
	)
);
if ( is_wp_error( $clothes_types ) ) {
	$clothes_types = array();
}
?>
<section class="create-clothes-item container">
	<h2 class="text-center">Create new item</h2>
	<form id="cci_form" autocomplete="off">

		<?php wp_nonce_field( 'cci_form', 'cci_form_nonce' ); ?>

		<input type="hidden" name="action" value="cci_form">

		<div class="cci_form_row">
			<label for="cci_clothes_name">Clothes name</label>
			<input class="form-required" id="cci_clothes_name" type="text" name="cci_clothes_name">
		</div>
		<div class="cci_form_row">
			<label for="cci_clothes_description">Description</label>
			<textarea class="form-required" id="cci_clothes_description" name="cci_clothes_description"></textarea>
		</div>

		<div class="cci_form_row">
			<label for="cci_clothes_image">Image</label>
			<input class="form-required" type="file" id="cci_clothes_image" name="cci_clothes_image">
		</div>

		<div class="cci_form_row">
			<hr>
		</div>

		<div class="cci_form_row">
			<label for="cci_clothes_size">Size</label>
			<input class="form-required" id="cci_clothes_size" type="text" name="cci_clothes_size">
		</div>
		<div class="cci_form_row">
			<label for="cci_clothes_color">Color</label>
			<input class="form-required" id="cci_clothes_color" type="text" name="cci_clothes_color">
		</div>
		<div class="cci_form_row">
			<label for="cci_clothes_sex">Sex</label>
			<input class="form-required" id="cci_clothes_sex" type="text" name="cci_clothes_sex">
		</div>
		<div class="cci_form_row">
			<label for="cci_clothes_type">Types</label>
			<select class="form-required" id="cci_clothes_type" name="cci_clothes_type[]" multiple>
				<?php foreach ( $clothes_types as $type_term ) : ?>
					<option value="<?php echo esc_attr( $type_term->term_id ); ?>">
						<?php echo esc_html( $type_term->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="cci_form_row">
			<div class="btn btn-secondary" id="cci_create">Create</div>
		</div>
	</form>
</section>

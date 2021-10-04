<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$tax_name            = 'clothes-type';
$clothes_terms       = wp_get_post_terms( get_the_ID(), $tax_name );
$clothes_terms_links = array();

foreach ( $clothes_terms as $term_obj ) {
	$term_url              = get_term_link( $term_obj, $tax_name );
	$term_name             = esc_html( $term_obj->name );
	$clothes_terms_links[] = "<a href='$term_url'>{$term_name}</a>";
}
$terms_string = join( ' | ', $clothes_terms_links );

$post_meta = get_post_meta( get_the_ID() );

$size  = $post_meta['size'][0] ?? '';
$color = $post_meta['color'][0] ?? '';
$sex   = $post_meta['sex'][0] ?? '';
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">


	<header class="entry-header">

		<div class="breadcrumb">
			<a class="breadcrumb-item active" href="<?php echo esc_url( home_url() ); ?>">home</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'clothes' ) ); ?>" class="breadcrumb-item active">clothes</a>
			<div class="breadcrumb-item"><?php the_title(); ?></div>
		</div>

		<div>
			<?php echo wp_kses_post( $terms_string ); ?>
		</div>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content">

		<div class="row">
			<div class="col-sm-4">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/no-image.jpg' ); ?>">
				<?php endif; ?>
			</div>

			<div class="col-sm-8">
				<?php the_content(); ?>

				<?php understrap_edit_post_link(); ?>
			</div>
		</div>

		<div class="meta-fields-values">
			<div>
				<strong>Sex: </strong><?php echo esc_html( $sex ); ?>
			</div>
			<div>
				<strong>Color: </strong><?php echo esc_html( $color ); ?>
			</div>
			<div>
				<strong>Size: </strong><?php echo esc_html( $size ); ?>
			</div>
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->

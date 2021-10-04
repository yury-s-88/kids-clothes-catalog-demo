<?php
/**
 * Search results partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$post_meta = get_post_meta( get_the_ID() );

$size  = $post_meta['size'][0] ?? '';
$color = $post_meta['color'][0] ?? '';
$sex   = $post_meta['sex'][0] ?? '';
?>

<div class="col-sm-6 col-md-4 col-lg-3">
	<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

		<header class="entry-header">

			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php if ( has_post_thumbnail() ) : ?> 
					<?php the_post_thumbnail( 'small' ); ?>
				<?php else : ?>
					<img width="360" height="360" alt="no image" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/no-image.jpg' ); ?>">
				<?php endif; ?>
			</a>

		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php
			the_title(
				sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
				'</a></h2>'
			);
			?>

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
		</div><!-- .entry-summary -->

	</article><!-- #post-## -->
</div>

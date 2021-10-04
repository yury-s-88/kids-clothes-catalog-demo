<?php
/**
 * @global $post
 */
$posts_query = new WP_Query(
	array(
		'post_type'      => 'clothes',
		'posts_per_page' => 10, // exactly 10 as was requested in specification
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

$clothes_posts        = $posts_query->posts;
$clothes_archive_link = get_post_type_archive_link( 'clothes' );
?>
<?php if ( $clothes_posts ) : ?>

	<h2 class="page-title">Latest clothes</h2>

	<div class="container container-clothes">

		<div class="row">

			<?php foreach ( $clothes_posts as $post ) : // overrides global $post; @codingStandardsIgnoreLine ?>

				<?php setup_postdata( $post ); ?>

				<?php get_template_part( 'loop-templates/content', 'clothes-card' ); ?>

			<?php endforeach; ?>

		</div><!--./row-->

	</div><!--./container-->

	<div class="text-center clothes-archive-link-container">
		<a href="<?php echo esc_url( $clothes_archive_link ); ?>">View all clothes</a>
	</div>
<?php endif; ?>

<?php wp_reset_postdata(); ?>

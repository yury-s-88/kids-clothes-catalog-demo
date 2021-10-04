<?php
/**
 * The template for displaying archive pages
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 * @global WP_User $current_user
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container           = get_theme_mod( 'understrap_container_type' );
$archive_description = get_the_archive_description();
$archive_title       = get_the_archive_title();
$current_term        = get_queried_object();
$term_id             = $current_term->term_id;
$term_name           = $current_term->name;
$image_id            = get_term_meta( $term_id, 'image', true );
$image               = wp_get_attachment_image( $image_id, 'medium' );

$edit_term_link = '';
if ( array_intersect( $current_user->roles, array( 'administrator', 'editor' ) ) ) {
	$edit_term_link = get_edit_term_link( $term_id );
}
?>
	<div class="wrapper" id="archive-wrapper">

		<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

			<div class="row">

				<!-- Do the left sidebar check -->
				<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

				<main class="site-main" id="main">

					<?php if ( have_posts() ) : ?>
						<header class="page-header">

							<div class="breadcrumb">
								<a class="breadcrumb-item active" href="<?php echo esc_url( home_url() ); ?>">home</a>
								<a href="<?php echo esc_url( get_post_type_archive_link( 'clothes' ) ); ?>" class="breadcrumb-item active">clothes</a>
								<div class="breadcrumb-item"><?php echo esc_html( $term_name ); ?></div>
							</div>

							<h1 class="page-title">
								<?php echo wp_kses_post( $archive_title ); ?>
							</h1>

							<div class="container">
								<div class="row">
									<div class="taxonomy-image col-sm-4 text-center">
										<?php echo wp_kses_post( $image ); ?>
									</div>
									<div class="taxonomy-description col-sm-8">
										<?php echo wp_kses_post( $archive_description ); ?>

										<?php if ( $edit_term_link ) : ?>
											<a class="post-edit-link" href="<?php echo esc_url( $edit_term_link ); ?>">Edit</a>
										<?php endif; ?>
									</div>

								</div>
							</div>

						</header><!-- .page-header -->
						<div class="container container-clothes">
							<div class="row">
								<?php while ( have_posts() ) : ?>
									<?php the_post(); ?>
									<?php get_template_part( 'loop-templates/content', 'clothes-card' ); ?>
								<?php endwhile; ?>
							</div>
						</div>
					<?php else : ?>
						get_template_part( 'loop-templates/content', 'none' );
					<?php endif; ?>
				</main><!-- #main -->

				<?php understrap_pagination(); ?>

			</div><!-- .row -->

		</div><!-- #content -->

	</div><!-- #archive-wrapper -->

<?php
get_footer();

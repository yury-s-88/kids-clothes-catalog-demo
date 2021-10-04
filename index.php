<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>
	<div class="wrapper" id="index-wrapper">

		<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

			<div class="row">


				<main class="site-main" id="main">

					<?php
					if ( have_posts() ) {
						// Start the Loop.
						while ( have_posts() ) {
							the_post();
							get_template_part( 'loop-templates/content', get_post_format() );
						}
					} else {
						get_template_part( 'loop-templates/content', 'none' );
					}
					?>

					<?php get_template_part( 'loop-templates/content', 'clothes-homepage' ); ?>

					<?php if ( array_intersect( wp_get_current_user()->roles, array( 'administrator', 'editor' ) ) ) : ?>

						<?php get_template_part( 'template-parts/create-clothes-form' ); ?>

					<?php endif; ?>
				</main><!-- #main -->

			</div><!-- .row -->

		</div><!-- #content -->

	</div><!-- #index-wrapper -->

<?php
get_footer();

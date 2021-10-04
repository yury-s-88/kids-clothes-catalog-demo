<?php
/**
 * The template for displaying archive pages
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

	<div class="wrapper" id="archive-wrapper">

		<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

			<div class="row">

				<main class="site-main" id="main">

					<?php if ( have_posts() ) : ?>
						<header class="page-header">
							<div class="breadcrumb">
								<a class="breadcrumb-item active" href="<?php echo esc_url( home_url() ); ?>">home</a>
								<div class="breadcrumb-item">clothes</div>
							</div>

							<?php
							the_archive_title( '<h1 class="page-title">', '</h1>' );
							the_archive_description( '<div class="taxonomy-description">', '</div>' );
							?>
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
						<?php get_template_part( 'loop-templates/content', 'none' ); ?>
					<?php endif; ?>

				</main><!-- #main -->



			</div><!-- .row -->

			<?php understrap_pagination(); ?>

		</div><!-- #content -->

	</div><!-- #archive-wrapper -->

<?php
get_footer();

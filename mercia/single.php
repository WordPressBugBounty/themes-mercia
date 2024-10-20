<?php
/**
 * The template for displaying all single posts.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Mercia
 */

get_header(); ?>

	<div id="content" class="site-content container clearfix">

		<section id="primary" class="content-single">
			<main id="main" class="site-main" role="main">

				<?php while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', 'single' );

				endwhile; ?>

			</main><!-- #main -->
		</section><!-- #primary -->

	</div><!-- #content -->

<?php get_footer(); ?>

<?php
/**
 * The template for rendering any single pages with no template.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.0.0
 */
?>

<?php
    get_header();
    include locate_template('parts/hero.php');
?>

	<main class="site-content container">

		<?php if (have_posts()): while (have_posts()): the_post(); ?>

			<article class="grid grid8_12">

				<div class="pad-1-1 copy">

					<h1><?php the_field('h1'); ?></h1>
					<?php the_content(); ?>

				</div>

			</article>

			<aside class="grid grid4_12">

				<?php get_sidebar(); ?>
			
			</aside>
	
		<?php endwhile; endif; ?>

	</main>

<?php
	include locate_template('parts/why-choose-us.php');
	include locate_template('parts/gallery.php');
?>

<?php get_footer(); ?>

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

<div class="container flex-wrap flex-grow lg:flex">

		<?php if (have_posts()): while (have_posts()): the_post(); ?>

			<main class="lg:w-2/3 lg:pr-16">

				<article>
					<h1><?php the_field('h1'); ?></h1>
					<?php the_content(); ?>
				</article>

			</main>

      <?php echo do_shortcode("[ninja_form id=1]"); ?>

			<aside class="bg-gray-100 lg:w-1/3">
				<?php get_sidebar(); ?>
			</aside>

		<?php endwhile; endif; ?>

</div>

<?php get_footer(); ?>

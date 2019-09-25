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

	<main class="container p-4 md:p-8 lg:px-0 lg:flex flex-wrap flex-grow">

			<?php if (have_posts()): while (have_posts()): the_post(); ?>

				<article class="lg:w-2/3 lg:pr-16">
					<h1><?php the_field('h1'); ?></h1>
					<?php the_content(); ?>
				</article>

        <?php echo do_shortcode("[ninja_form id=1]"); ?>

				<aside class="bg-gray-100 lg:w-1/3">
					<?php get_sidebar(); ?>
				</aside>

			<?php endwhile; endif; ?>

	</main>

<?php get_footer(); ?>

<?php
/**
 * The template for rendering anything not caught by other file as well
 * as the loop for any blog posts. In theory this is a "fallback" file. 
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

	<main class="lg:w-2/3 lg:pr-16">

		<?php if (have_posts()): while (have_posts()): the_post(); ?>

			<article>
				<?php the_post_thumbnail('medium'); ?>
				<?php the_title('<h1>', '</h1>'); ?>
				<?php the_excerpt(); ?>
				<a href="<?php the_permalink(); ?>" class="btn">Continue reading</a>
			</article>

		<?php endwhile; endif; ?>

		<div class="prev-next">				
			<div class="prev"><?php previous_posts_link(); ?></div>
			<div class="next"><?php next_posts_link(); ?></div>
		</div>	

	</main>

	<aside class="bg-gray-100 lg:w-1/3">
		<?php get_sidebar(); ?>
	</aside>

</div>

<?php get_footer(); ?>
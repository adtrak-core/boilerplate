<?php
/**
 * The template for rendering any single posts.
 * This is the template of single blog posts and custom post types. 
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.1.0
 */
?>

<?php get_header(); ?>

	<section>
		<?php if (have_posts()): while (have_posts()): the_post(); ?>
		
			<article>
				<?php the_title('<h1>', '</h1>'); ?>
				<?php the_content(); ?>
			</article>
	
		<?php endwhile; else: ?>
		
			<p>Nothing to see.</p>
	
		<?php endif; ?>
	</section>

<?php get_footer(); ?>

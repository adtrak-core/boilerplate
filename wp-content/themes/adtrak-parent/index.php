<?php
/**
 * The template for rendering anything not caught by other file as well
 * as the loop for any blog posts. In theory this is a "fallback" file. 
 * @author  Adtrak
 * @package AdtrakParent
 * @version 1.0.0
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

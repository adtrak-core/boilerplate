<?php
/**
 * The template for the front-page of the site. This will be the
 * landing page of your site.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.0.0
 */

get_header(); ?>

	<section>
		<?php if (have_posts()): while (have_posts()): the_post(); ?>
			
			<?php the_title('<h1>', '</h1>'); ?>
			<?php the_content(); ?>
	
		<?php endwhile; else: ?>
		
			<p>Nothing to see.</p>
	
		<?php endif; ?>
	</section>

<?php get_footer(); ?>

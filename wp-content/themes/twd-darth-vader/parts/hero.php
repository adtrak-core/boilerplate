<?php 

if ( is_front_page() ) { 

/* -----------------------------------------------------------------
Home page
----------------------------------------------------------------- */

?>

	<div class="hero">

	<?php

	/* This uses the featured image as a background. Takes the featured image, and applies the different sizes to varying breakpoints. */

	$thumb_id = get_post_thumbnail_id();

	$thumb_url_array_small = wp_get_attachment_image_src($thumb_id, 'hero-600', true);
	$thumb_url_small = $thumb_url_array_small[0];

	$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'hero-1200', true);
	$thumb_url = $thumb_url_array[0];

	$thumb_url_array_large = wp_get_attachment_image_src($thumb_id, 'hero-2000', true);
	$thumb_url_large = $thumb_url_array_large[0];

	if ( $thumb_id ) : ?>

		<style>
			.hero {
		      background-image: url(<?php echo $thumb_url_small; ?>);
		    }
		    @media (min-width: 600px) {
				.hero {
			       background-image: url(<?php echo $thumb_url; ?>);
			    }
		    }
		    @media (min-width: 1200px) {
				.hero {
			      background-image: url(<?php echo $thumb_url_large; ?>);
			    }
		    }
		</style>

	<?php endif; ?>

		<div class="container">

			<div class="hero-content">

				<p class="hero-primary"><?php the_field('hero_primary'); ?></p>
				<p class="hero-secondary"><?php the_field('hero_secondary'); ?></p>

				<?php if (have_rows('hero_ticks')) :?>

				<ul class="hero-ticks">
					<?php while( have_rows('hero_ticks') ): the_row(); ?>
						<li><?php the_sub_field('item'); ?></li>
					<?php endwhile; ?>
				</ul>

				<?php endif; ?>

			</div>	

		</div>

	</div>

<?php } elseif (is_page('contact') || is_page('contact-us')) {

/* -----------------------------------------------------------------
Contact page
----------------------------------------------------------------- */

?>



<?php } elseif (is_page()) {

/* -----------------------------------------------------------------
Internal page
----------------------------------------------------------------- */

?>



<?php } elseif (is_singular('post')) { 

/* -----------------------------------------------------------------
Single post
----------------------------------------------------------------- */

?>



<?php } elseif (is_search()) {

/* -----------------------------------------------------------------
Search results
----------------------------------------------------------------- */

?>



<?php } else {

/* -----------------------------------------------------------------
Everything else
----------------------------------------------------------------- */

?>

	

<?php } ?>
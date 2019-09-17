<?php

if ( is_front_page() ) {

/* -----------------------------------------------------------------
Home page
----------------------------------------------------------------- */

?>

	<div>

	<?php

	/* This uses the featured image as a background. Takes the featured image, and applies the different sizes to varying breakpoints. */

	$thumb_id = get_post_thumbnail_id();
	$thumb_url_array_small = wp_get_attachment_image_src($thumb_id, 'img-600-600', true);
	$thumb_url_small = $thumb_url_array_small[0];
	$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'img-1200-500', true);
	$thumb_url = $thumb_url_array[0];
	$thumb_url_array_large = wp_get_attachment_image_src($thumb_id, 'img-2000-650', true);
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

		<div class="hero min-h-64 bg-cover bg-center lg:min-h-75">

			<div class="container text-white">

			<?php

				// check if the flexible hero has rows of data
				if( have_rows('flexible_hero') ):

				     // loop through the rows of data
				    while ( have_rows('flexible_hero') ) : the_row();

				        if( get_row_layout() == 'text' ): ?>

				        	<p class="<?php the_sub_field('hero_text_class'); ?>"><?php the_sub_field('hero_text'); ?></p>

				        <?php

				        elseif( get_row_layout() == 'button' ): ?>

							<a class="<?php the_sub_field('button_class'); ?>" href="<?php the_sub_field('button_link') ?>">
								<?php the_sub_field('button_text') ?>
							</a>

				    	<?php endif;

				    endwhile; ?>

				<?php /* Else display the title */ else : ?>

				<p><?php the_title(); ?></p>

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



<?php } elseif (is_singular()) {

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

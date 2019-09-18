<?php

	// Choose image size
	$imageSize = "img-350-350";

	// Get Bucket Layout Choice
	if 		(get_field('buckets_layout') == 'nomargin') { 			$bucketsLayout = 'no-margins'; }
	elseif 	(get_field('buckets_layout') == 'marginsbetween') { 	$bucketsLayout = 'in-between'; }

	// Get Image Type Choice (background image or HTML image)
	if (get_field('buckets_image_type') == "backgroundimage") { 	$imageType = "imagebg"; }
	elseif (get_field('buckets_image_type') == "htmlimage") { 		$imageType = "imagehtml"; }

	/* -----------------------------------------------
	--------------------------------------------------
	Start custom buckets
	--------------------------------------------------
	----------------------------------------------- */

	if (get_field('relationship_or_custom') == "custom") :

	// Get number of buckets
	$bucketCount = count(get_field('custom_buckets'));

?>


	<ul class="buckets buckets--num-<?php echo $bucketCount ." buckets--". $bucketsLayout ?>">

		<?php
		
		while( has_sub_field('custom_buckets') ): ?>

			<?php

				// Take display text, make it CSS friendly
	 			$displayTextCondensed = str_replace(" ", "-", strtolower(get_sub_field('bucket_display_text')));

	 			// Get image field
				$image = get_sub_field('bucket_image');

				// Get image
				$thumb = $image['sizes'][ $imageSize ];

			

			// Buckets with background images
			// ------------------------------------------------------------------------------------
			if($imageType == 'imagebg'): ?>
			<style>
				.buckets .buckets__item--<?php echo $displayTextCondensed; ?> {
					background-image: url("<?php echo $thumb; ?>");
				}
			</style>

	 		<li class="buckets__item buckets__item--<?php echo $displayTextCondensed; ?>">
	 			<a class="buckets__link" href="<?php the_sub_field('bucket_link') ?>">

	 				<span class="buckets__title"><?php the_sub_field('bucket_display_text') ?></span>

	 			</a>
	 		</li>




	 		<?php
			// Buckets with HTML images
			// ------------------------------------------------------------------------------------
			elseif($imageType == 'imagehtml'): ?>

	 		<li class="buckets__item buckets__item-<?php echo $displayTextCondensed; ?>">
	 			<a class="buckets__link" href="<?php the_sub_field('bucket_link') ?>">

	 				<img class="buckets__image" src="<?php echo $thumb; ?>" alt="<?php the_sub_field('bucket_display_text') ?>">
	 				
					<span class="buckets__title"><?php the_sub_field('bucket_display_text') ?></span>

	 			</a>
	 		</li>
	 		<?php endif; ?>





		<?php endwhile; ?>

	 </ul>

<?php

	/* -----------------------------------------------
	--------------------------------------------------
	Start relationship buckets
	--------------------------------------------------
	----------------------------------------------- */

	elseif (get_field('relationship_or_custom') == "relationship") :

	// Set variable to increase
	$i = 1;

	// Get bucket data
	$bucketposts = get_field('relationship_buckets');

	// Get number of buckets
	$bucketCount = count(get_field('relationship_buckets'));

?>

	<ul class="buckets buckets--num-<?php echo $bucketCount ." buckets--". $bucketsLayout ?>">

		<?php foreach( $bucketposts as $p ):

			/* This uses the featured image as a background. Takes the featured image, and applies the different sizes to varying breakpoints. */

			$thumb_id = get_post_thumbnail_id( $p->ID );
			$bucket_array = wp_get_attachment_image_src($thumb_id, $imageSize, true);
			$bucket_url = $bucket_array[0];





			// Buckets with background images
			// ------------------------------------------------------------------------------------
			if($imageType == 'imagebg'): ?>
				<style>
					.buckets__item--<?php echo $i; ?> {
				      background-image: url(<?php echo $bucket_url; ?>);
				    }
				</style>

				<li class="buckets__item buckets__item--<?php echo $i; ?>">
					<a class="buckets__link" href="<?php echo get_permalink( $p->ID ); ?>">

					<span class="buckets__title"><?php echo get_the_title( $p->ID ); ?></span>

					</a>
				</li>







	 		<?php
			// Buckets with HTML images
			// ------------------------------------------------------------------------------------
			elseif($imageType == 'imagehtml'): ?>

			<li class="buckets__item buckets__item--<?php echo $i; ?>">
				<a class="buckets__link" href="<?php echo get_permalink( $p->ID ); ?>">

	 				<img class="buckets__image" src="<?php echo $bucket_url; ?>" alt="<?php the_sub_field('bucket_display_text') ?>">

					<span class="buckets__title"><?php echo get_the_title( $p->ID ); ?></span>

				</a>
			</li>

	 		<?php endif; ?>






		<?php $i++;

		endforeach; ?>

	</ul>

<?php endif; ?>
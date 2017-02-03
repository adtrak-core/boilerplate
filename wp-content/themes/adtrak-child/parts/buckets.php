	<?php

	// Set variable to increase
	$i = 1;

	// Get bucket data
	$posts = get_field('buckets');

	// If data exists, count them			
	if( $posts ): $bucketCount = count(get_field('buckets'));

	$bucketsLayout = "no-margins";

	// Get bucket layout
	if (get_field('bucket_layout') == 'marginsbetween') { $bucketsLayout = 'in-between'; }

	?>

		<ul class="buckets num-<?php echo $bucketCount ." ". $bucketsLayout ?>">

			<?php foreach( $posts as $p ): // variable must NOT be called $post (IMPORTANT) 

				/* This uses the featured image as a background. Takes the featured image, and applies the different sizes to varying breakpoints. */

				$thumb_id = get_post_thumbnail_id( $p->ID );
				$bucket_array = wp_get_attachment_image_src($thumb_id, 'hero-600', true);
				$bucket_url = $bucket_array[0];

				if ( $thumb_id ) : ?>

					<style>
						.bucket-<?php echo $i; ?> {
					      background-image: url(<?php echo $bucket_url; ?>);
					    }
					</style>

				<?php else : ?>

					<style>
						.bucket-<?php echo $i; ?> {
					      background: #eee;
					    }
					</style>

				<?php endif; ?>

				<li class="bucket-<?php echo $i; ?>"><a href="<?php echo get_permalink( $p->ID ); ?>">

					<span class="title"><?php echo get_the_title( $p->ID ); ?></span>

					<?php /* If you want to use the excerpt, this needs to be hand-set on the page in WordPress */ ?>
					<p><?php echo get_the_excerpt( $p->ID ); ?></p>

				</a></li>

			<?php $i++; endforeach; ?>

		</ul>

	<?php endif; ?>
				<?php if (have_rows('global_ctas','options')) : $i = 1; ?>
					<?php while( have_rows('global_ctas','options') ): the_row(); ?>

					<div class="pad-1-1 cta cta-<?php echo $i; ?>">

						<?php
							//	Get the CTA background image
							$image = get_sub_field('cta_background_image','options');
							$thumb = $image['sizes'][ 'square-350' ];
						?>

						<style>

							.cta-<?php echo $i; ?>:after {
								background-image: url("<?php echo $thumb; ?>");
							}

						</style>

						<h2><?php the_sub_field('primary_text'); ?></h2>
						<p><?php the_sub_field('secondary_text'); ?></p>
						
						<a href="<?php the_sub_field('button_link'); ?>" class="<?php the_sub_field('button_class'); ?>">
							<?php the_sub_field('button_text'); ?>
						</a>

					</div>

					<?php $i++; endwhile; ?>
				<?php endif; ?>
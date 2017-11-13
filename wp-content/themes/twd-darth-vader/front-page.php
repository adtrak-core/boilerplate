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
    include locate_template('parts/buckets.php');
    include locate_template('parts/why-choose-us.php');
?>

	<main class="site-content container">

		<?php if (have_posts()): while (have_posts()): the_post(); ?>

			<article class="grid grid8_12">

				<div class="pad-1-1 copy">

					<h1><?php the_field('h1'); ?></h1>
					<?php the_content(); ?>

				</div>

			</article>

			<aside class="grid grid4_12">






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








				<?php get_sidebar(); ?>
			
			</aside>
	
		<?php endwhile; endif; ?>

	</main>

<?php include locate_template('parts/gallery.php'); ?>

<?php get_footer(); ?>

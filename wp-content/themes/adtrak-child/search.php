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

	<main class="site-content">
		<div class="container">
			<?php if (have_posts()): ?>

			<div class="copy">

				<h1>Results for <?php the_search_query(); ?> </h1>

				<?php while (have_posts()): the_post(); ?>

					<?php /* Check if it is a post, not a page - remove this if you want to have site-wide search */ if ( 'post' == get_post_type() ) : ?>

						<article class="post post--overview">						
							<?php the_post_thumbnail('medium'); ?>

							<?php the_title( sprintf( '<h2><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
							<?php the_excerpt(); ?>

							<a href="<?php the_permalink(); ?>" class="btn btn--noir">Continue reading</a>
						</article>

					<?php endif; ?>
			
				<?php endwhile; ?>

					<div class="prev-next">				
						<div class="prev"><?php previous_posts_link(); ?></div>
						<div class="next"><?php next_posts_link(); ?></div>		
			        </div>

			    <?php else : ?>

					<h1>Sorry, no results found.</h1>

				<?php endif; ?>

			</div>

			<aside class="sidebar sidebar--news">
				<?php get_sidebar(); ?>		
			</aside>
		</div>
	</main>

<?php get_footer(); ?>
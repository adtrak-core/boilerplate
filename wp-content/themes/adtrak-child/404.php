<?php
/**
 * The template for displaying 404 pages within your theme.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.0.0
 */

get_header(); ?>

	<main class="site-content container">

		<article class="grid grid12_12">

			<div class="pad-10-10 copy">

				<h1>Sorry!</h1>

				<p>This page can't be found.</p>

				<p><a href="<?php echo site_url('/'); ?>">Please go home</a></p>

			</div>

		</article>

	</main>
	
<?php get_footer(); ?>
<?php
/**
 * The template for displaying the footer within your theme.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.0.0
 */
?>
	
	</main>

	<footer>
		<div>
			<a href="<?= home_url(); ?>">
				<img class="logo logo--footer" src="<?= get_theme_file_uri('/images/logo.svg'); ?>" alt="<?php bloginfo('title'); ?> Logo" />
			</a>
		</div>

		<div>
			<h6>Explore</h6>
			<?php wp_nav_menu([
				'menu' => 'Footer Menu', 
				'menu_class' => 'nav nav--footer', 
				'container' => '' 
			]); ?>
		</div>

		<div>
			<p>&copy; <?= get_bloginfo('name'); ?> <?= date('Y'); ?>. All Rights Reserved</p>				
			<p><a href="<?= site_url('privacy-policy'); ?>">Cookies &amp; Privacy Policy</a></p>
			<?php 
			/** 
				* get_adtrak_logo accepts two arguments 
				* 'colour' (as white, black, orange/default) and 
				* 'icon' (as true or false) 
				* e.g. for the black icon use get_adtrak_logo('black', true)
			*/ ?>
			<p><a class="adtrak" href="https://www.adtrak.co.uk" role="outgoing"><?php echo get_adtrak_logo(); ?></a></p>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>

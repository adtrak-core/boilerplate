<?php
/**
 * The template for displaying the footer within your theme.
 * @author  Adtrak
 * @package AdtrakParent
 * @version 2.1.0
 */
?>
	
	</main>

	<footer>
		<div>
			<a href="<?php echo home_url(); ?>">
				<img class="logo logo--footer" src="<?php echo get_theme_file_uri('/images/logo.svg'); ?>" alt="<?php bloginfo('title'); ?> Logo" />
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
			<p><a href="<?= site_url('cookies-privacy-policy/'); ?>">Cookies &amp; Privacy Policy</a></p>
			<p><a class="adtrak" href="https://adtrak.co.uk" role="outgoing"><?php echo get_adtrak_logo(); ?></a></p>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>

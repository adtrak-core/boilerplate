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
			<p>&copy; <?php echo get_bloginfo('name'); ?> <?php echo date('Y'); ?>. All Rights Reserved</p>				
			<p><a href="<?php echo site_url('cookies-privacy-policy/'); ?>">Cookies &amp; Privacy Policy</a></p>
			<p><a class="adtrak" href="https://adtrak.co.uk" role="outgoing"><img src="http://static.adtrak.co.uk/email/201504/svg/adtrak-logo.svg" alt="Adtrak"></a></p>
		</div>
	</footer>

	<?php get_template_part('parts/ld.php'); ?>
	<?php wp_footer(); ?>
</body>
</html>

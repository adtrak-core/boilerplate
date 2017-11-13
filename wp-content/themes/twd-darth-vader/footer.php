<?php
/**
 * The template for displaying the footer within your theme.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.1.0
 */
?>

	<footer class="pad-1-1">
		<div class="container">

			<div class="grid grid3_12">
				<h6>Explore</h6>
				<?php wp_nav_menu([
					'menu' => 'Primary Menu', 
					'menu_class' => 'nav nav--footer', 
					'container' => '' 
				]); ?>
			</div>

			<div class="grid grid3_12">
				<h6>More Info</h6>
				<?php wp_nav_menu([
					'menu' => 'Secondary Menu', 
					'menu_class' => 'nav nav--footer', 
					'container' => '' 
				]); ?>
			</div>

			<div class="grid grid3_12">
				<h6>Get in touch</h6>
		        <p><?php address_stacked(); ?></p>
		        <p><a href="mailto:<?php echo get_field('site_email', 'option'); ?>"><?php the_field('site_email', 'option'); ?></a></p>
		        <p><?php do_action("ld_default",false,true); ?></p>
		    </div>

			<div class="grid grid3_12">
				<h6>Another Nav Space</h6>
				<?php wp_nav_menu([
					'menu' => 'Secondary Menu', 
					'menu_class' => 'nav nav--footer', 
					'container' => '' 
				]); ?>
		    </div>

			<div class="grid grid12_12 bottom-row">
				<div class="grid grid9_12">
			        <p><?php bloginfo('title'); ?> is a registered company in England.
					<?php if(get_field('reg_number', 'option')): ?>
						&bull; Registered Number: <?php the_field('reg_number', 'option'); ?>
					<?php endif; ?>
					<?php if(get_field('vat_number', 'option')): ?>
						&bull; VAT Number: <?php the_field('vat_number', 'option'); ?>
					<?php endif; ?>
			        &copy; <?php bloginfo('title'); ?> <?php echo date('Y'); ?>. All Rights Reserved</p>
			    </div>
				<div class="grid grid3_12">
					<?php 
					/** 
					 * get_adtrak_logo accepts two arguments 
					 * 'colour' (as white, black, orange/default) and 
					 * 'icon' (as true or false) 
					 * e.g. for the black icon use get_adtrak_logo('black', true)
					*/ ?>
			        <a class="adtrak" href="https://www.adtrak.co.uk"><?php echo get_adtrak_logo(); ?></a>
			    </div>
		    </div>
		</div>
	</footer>

	<!-- Back to top arrow -->
	<div class="back-top-wrap">
	    <p id="back-top">
	        <a><i class="fa fa-arrow-up fa-2x"></i> Top</a>
	    </p>
	</div>

</div><!-- wrapper -->

<?php wp_footer(); ?>
	
</body>
</html>

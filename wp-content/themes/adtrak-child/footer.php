<?php
/**
 * The template for displaying the footer within your theme.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.0.0
 */
?>

	<footer role="contentinfo">

		<div class="container">

			<div class="grid grid3_12">
				<a href="<?php echo home_url(); ?>">
					<?php $image = get_field('site_logo','option'); if( !empty($image) ): ?>
						<img src="<?php echo $image['url']; ?>" alt="<?php bloginfo('title'); ?> Logo" />
					<?php endif; ?>
				</a>
			</div>

			<div class="grid grid3_12">
				<h6>Explore</h6>
				<?php wp_nav_menu([
					'menu' => 'Footer Menu', 
					'menu_class' => 'nav nav--footer', 
					'container' => '' 
				]); ?>
			</div>

			<div class="grid grid6_12">

		        <p>Address: <?php address_inline(); ?></p>
		        <p>Email: <a href="mailto:<?php the_field('site_email', 'option'); ?>"><?php the_field('site_email', 'option'); ?></a></p>
		        <p><?php the_field('site_name', 'option'); ?> is a registered company in England.</p>
		        <p><?php if(get_field('company_reg_number', 'option')) : ?>Registered Number: <?php the_field('company_reg_number', 'option'); ?></p><?php endif; ?>
		        <p><?php if(get_field('company_vat_number', 'option')) : ?>VAT Number: <?php the_field('company_vat_number', 'option'); ?></p><?php endif; ?>
		        <p>&copy; <?php the_field('site_name', 'option'); ?> <?php echo date('Y'); ?>. All Rights Reserved</p>

		        <a class="adtrak" href="https://www.adtrak.co.uk" role="outgoing"><img src="http://static.adtrak.co.uk/email/201504/svg/adtrak-logo.svg" alt="Adtrak" /></a>
		        <a class="adtrak" href="https://www.adtrak.co.uk" role="outgoing"><img src="http://static.adtrak.co.uk/email/201504/svg/adtrak-logo-white.svg" alt="Adtrak" /></a>
		        <a class="adtrak" href="https://www.adtrak.co.uk" role="outgoing"><img src="http://static.adtrak.co.uk/email/201504/svg/adtrak-logo-black.svg" alt="Adtrak" /></a>

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

	<?php include locate_template('parts/ld.php'); ?>
	<?php wp_footer(); ?>
	
</body>
</html>

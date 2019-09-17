<?php
/**
 * The template for displaying the footer within your theme.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.1.0
 */
?>

	<footer class="p-4 bg-gray-100 text-center md:p-8 lg:px-0">

		<div class="container lg:flex flex-wrap">

			<div class="w-full mb-8 lg:w-3/12">
				<a class="block" href="<?php echo home_url(); ?>">
					<?php $image = get_field('site_logo','option'); if( !empty($image) ): ?>
						<img class="block mx-auto lazyload max-w-32" data-src="<?php echo $image['url']; ?>" alt="<?php bloginfo('title'); ?> Logo" />
					<?php endif; ?>
				</a>
			</div>

			<div class="w-full mb-8 lg:w-3/12">
				<h6>Explore</h6>
				<?php wp_nav_menu([
					'menu' => 'Footer Menu',
					'menu_class' => 'nav nav--footer',
					'container' => ''
				]); ?>
			</div>

			<div class="w-full lg:w-1/2">
        <p>Address: <?php address_stacked(); ?></p>
        <p>Email: <a href="mailto:<?php echo get_field('site_email', 'option'); ?>"><?php the_field('site_email', 'option'); ?></a></p>
        <p><?php bloginfo('title'); ?> is a registered company in England.</p>

				<?php if(get_field('reg_number', 'option')): ?>
					<p>Registered Number: <?php the_field('reg_number', 'option'); ?></p>
				<?php endif; ?>

				<?php if(get_field('vat_number', 'option')): ?>
					<p>VAT Number: <?php the_field('vat_number', 'option'); ?></p>
				<?php endif; ?>

		    <p>&copy; <?php bloginfo('title'); ?> <?php echo date('Y'); ?>. All Rights Reserved</p>
				<?php
				/**
				 * get_adtrak_logo accepts two arguments
				 * 'colour' (as white, black, orange/default) and
				 * 'icon' (as true or false)
				 * e.g. for the black icon use get_adtrak_logo('black', true)
				*/ ?>
		      <a class="block mx-auto max-w-32 adtrak" href="https://www.adtrak.co.uk"><?php echo get_adtrak_logo(); ?></a>
		    </div>

		</div>

	</footer>

	<!-- Back to top arrow -->
  <p id="back-top" class="border-rounded fixed bottom-4 right-4 z-100 opacity-50 text-center bg-white-100 p-4">
      <a class="text-black no-underline text-sm">
				<i class="fa fa-arrow-up fa-2x text-md"></i> <span class="block text-lg uppercase">Top</span>
			</a>
  </p>


</div><!-- wrapper -->


<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/scripts/lazysizes.min.js" async=""></script>

<?php wp_footer(); ?>

</body>
</html>

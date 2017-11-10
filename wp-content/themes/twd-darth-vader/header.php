<?php
/**
 * The header output and functionality. Most of the output of CSS, JS, 
 * etc will be carried out via the functions and there should be minimal
 * need to change anything in the head.
 * @author  Adtrak
 * @package AdtrakChild
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  	<meta charset="<?php bloginfo('charset'); ?>">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1">

  	<?php wp_head(); ?>

	<?php if (get_field('google_analytics', 'options')) echo get_field('google_analytics', 'options'); ?>
	<?php if (get_field('schema', 'options')) echo get_field('schema', 'options'); ?>


	<?php /* Typekit async loading
	
	<script>
	   WebFontConfig = {
	      typekit: { id: 'xxxxxx' }
	   };

	   (function(d) {
	      var wf = d.createElement('script'), s = d.scripts[0];
	      wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
	      wf.async = true;
	      s.parentNode.insertBefore(wf, s);
	   })(document);
	</script> */ ?>

</head>

<body <?php body_class(); ?>>

<div class="wrapper">

	<?php include locate_template('parts/mobile-top-bar.php'); ?>

	<header>

		<div class="top-bar">

			<div class="container">

				<?php /* run.js will move the secondary nav in here on desktop */ ?>
			
			</div>

		</div>

		<div class="container">

			<a href="<?php echo home_url(); ?>">
				<?php $image = get_field('site_logo','option'); if( !empty($image) ): ?>
					<?php /* Logo is deferred - see run.js */ ?>
					<img class="logo" src="" data-src="<?php echo $image['url']; ?>" alt="<?php bloginfo('title'); ?> Logo" />
				<?php endif; ?>
			</a>

	        <?php include locate_template('parts/phone-top-right.php'); ?>

	    </div>

		<nav id="navigation">
			<div>
				<?php 
				// Both primary and secondary menus for Mmenu
					wp_nav_menu([
						'menu' => 'Primary Menu', 
						'menu_class' => "menu-primary", 
						'container' => ''
					]);
					wp_nav_menu([
						'menu' => 'Secondary Menu', 
						'menu_class' => "menu-secondary", 
						'container' => ''
					]);
				?>
			</div>
		</nav>

	</header>

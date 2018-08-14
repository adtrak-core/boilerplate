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

	<?php /* Path dependent critical CSS */ ?>
  	<style type="text/css">
  		<?php include ('css/critical.css'); ?>
        .mob-nav, .mob-nav-underlay { display: none; }
  	</style>

  	<?php /* Load CSS async */ ?>
  	<script>
		function loadCSS(e,t,n){"use strict";function o(){var t;for(var i=0;i<s.length;i++){if(s[i].href&&s[i].href.indexOf(e)>-1){t=true}}if(t){r.media=n||"all"}else{setTimeout(o)}}var r=window.document.createElement("link");var i=t||window.document.getElementsByTagName("script")[0];var s=window.document.styleSheets;r.rel="stylesheet";r.href=e;r.media="only x";i.parentNode.insertBefore(r,i);o();return r}

		loadCSS( "<?php echo get_theme_file_uri('/css/main.css'); ?>" );
	</script>

  	<?php /* No JS support */ ?>
	<noscript>
		<link rel="stylesheet" href="<?php echo get_theme_file_uri('/css/main.css'); ?>">
	</noscript>

  	<?php wp_head(); ?>

	<?php
		if (get_field('google_analytics', 'options')) echo get_field('google_analytics', 'options');
		if (get_field('schema', 'options')) echo get_field('schema', 'options');
	?>

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

	<nav class="mob-nav">
		<div class="scroll-container"><?php /* Primary and Secondary menus will be cloned into here to form the mobile nav */ ?></div>
	</nav>

	<div class="wrapper">

		<?php include locate_template('parts/mobile-top-bar.php'); ?>

		<header>

			<div class="top-bar">

				<div class="container">

					<?php
					// Secondary Menu
						wp_nav_menu([
							'menu' => 'Secondary Menu',
							'menu_class' => "menu-secondary",
							'container' => ''
						]);
					?>

				</div>

			</div>

			<div class="container">

				<a href="<?php echo home_url(); ?>">
					<?php $image = get_field('site_logo','option'); if( !empty($image) ): ?>
						<img class="logo" src="<?php echo $image['url']; ?>" alt="<?php bloginfo('title'); ?> Logo" />
					<?php endif; ?>
				</a>

		        <?php include locate_template('parts/phone-top-right.php'); ?>

		    </div>

			<nav id="navigation" class="desktop-nav">
				<div>
					<?php
					// Primary menu for desktop
						wp_nav_menu([
							'menu' => 'Primary Menu',
							'menu_class' => "menu-primary",
							'container' => ''
						]);
					?>
				</div>
			</nav>

		</header>

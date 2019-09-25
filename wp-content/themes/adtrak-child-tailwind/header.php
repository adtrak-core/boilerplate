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

		<link rel="stylesheet" href="<?php echo get_theme_file_uri('/css/main.min.css'); ?>">

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
		<div class="scroll-container">
      <?php /* Primary and Secondary menus will be cloned into here to form the mobile nav */ ?>
    </div>
	</nav>

	<div class="wrapper">

		<?php include locate_template('parts/mobile-top-bar.php'); ?>

		<header class="pt-10 lg:pt-0">

			<div class="top-bar hidden bg-gray-100 lg:block">

				<div class="container">

					<?php
						wp_nav_menu([
							'menu' => 'Secondary Menu',
							'menu_class' => "menu-secondary",
							'container' => '',
              'items_wrap'      => '<ul id="%1$s" class="%2$s w-full flex flex-wrap justify-center">%3$s</ul>',
						]);
					?>

				</div>

			</div>

			<div class="container lg:py-4 lg:flex flex-wrap justify-between items-center">

				<a class="block p-4 lg:inline-block" href="<?php echo home_url(); ?>">
					<?php
            $image = get_field('site_logo','option');
            if( !empty($image) ): ?>
						      <img class="mx-auto max-w-40" src="<?php echo $image['url']; ?>" alt="<?php bloginfo('title'); ?> Logo" />
					<?php endif; ?>
				</a>

		    <?php include locate_template('parts/phone-top-right.php'); ?>

		   </div>

			<nav id="navigation" class="hidden bg-blue-100 container lg:block">
					<?php
					// Primary menu for desktop
						wp_nav_menu([
							'menu' => 'Primary Menu',
							'menu_class' => "menu-primary",
							'container' => '',
              'items_wrap'      => '<ul id="%1$s" class="%2$s w-full flex flex-wrap justify-center">%3$s</ul>',
						]);
					?>
			</nav>

		</header>

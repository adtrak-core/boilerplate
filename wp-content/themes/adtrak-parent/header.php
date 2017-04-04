<?php
/**
 * The header output and functionality. Most of the output of CSS, JS,
 * etc will be carried out via the functions and there should be minimal
 * need to change anything in the head.
 * @author  Adtrak
 * @package AdtrakParent
 * @version 2.0.0
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
</head>

<body <?php body_class(); ?>>
	<header role="header">
		<div class="logo">
			<h1><img src="<?php echo get_theme_file_uri('/images/logo.svg'); ?>" alt="<?php echo get_bloginfo('name'); ?>" class="logo__image"></h1>
		</div>

		<nav role="navigation">
			<?php wp_nav_menu([
				'menu' => 'Primary Menu', 
				'menu_class' => "nav nav--header", 
				'container' => ''
			]); ?>
		</nav>
	</header>

	<main class="site-content">
	
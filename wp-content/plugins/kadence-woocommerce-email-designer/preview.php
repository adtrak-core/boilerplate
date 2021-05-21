<?php
/**
 * View for email preview
 *
 * @package Kadence Woocommerce Email Designer
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width" />

		<title><?php echo __( 'Email Designer', 'kadence-woocommerce-email-designer' ); ?></title>

		<style type="text/css" id="kadence_woomail_designer_custom_css"><?php echo Kadence_Woomail_Customizer::opt( 'custom_css' ); ?>.woocommerce-store-notice.demo_store, .mfp-hide {display: none;}</style>

	</head>

	<body>

		<div id="kt_woomail_preview_wrapper" style="display: block;">

			<?php Kadence_Woomail_Preview::print_preview_email(); ?>

		</div>

		<?php
		do_action( 'woomail_footer' );
		wp_footer();
		?>

	</body>

</html>

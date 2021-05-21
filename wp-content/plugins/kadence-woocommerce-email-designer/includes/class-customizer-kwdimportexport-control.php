<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
	exit;
}
/**
 * This is a customized version of https://wordpress.org/plugins/customizer-export-import/
 */
if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'WP_Customize_Kwdimportexport_Control' ) ) {
	class WP_Customize_Kwdimportexport_Control extends WP_Customize_Control {
		public $type = 'kwdimportexport';

		public function render_content() {
			?>
			<span class="customize-control-title">
				<?php _e( 'Export', 'kadence-woocommerce-email-designer' ); ?>
			</span>
			<span class="description customize-control-description">
				<?php _e( 'Click the button below to export the customization settings for this plugin.', 'kadence-woocommerce-email-designer' ); ?>
			</span>
			<input type="button" class="button button-primary kadence-woomail-export kadence-woomail-button" name="kt-woomail-export-button" value="<?php esc_attr_e( 'Export', 'kadence-woocommerce-email-designer' ); ?>" />

			<hr class="kt-woomail-hr" />

			<span class="customize-control-title">
				<?php _e( 'Import', 'kadence-woocommerce-email-designer' ); ?>
			</span>
			<span class="description customize-control-description">
				<?php _e( 'Upload a file to import customization settings for this plugin.', 'kadence-woocommerce-email-designer' ); ?>
			</span>
			<div class="kadence-woomail-import-controls">
				<input type="file" name="kadence-woomail-import-file" class="kadence-woomail-import-file" />
				<?php wp_nonce_field( 'kt-woomail-importing', 'kt-woomail-import' ); ?>
			</div>
			<div class="kadence-woomail-uploading"><?php _e( 'Uploading...', 'kadence-woocommerce-email-designer' ); ?></div>
			<input type="button" class="button button-primary kadence-woomail-import kadence-woomail-button" name="kt-woomail-import-button" value="<?php esc_attr_e( 'Import', 'kadence-woocommerce-email-designer' ); ?>" />
			<?php
		}
	}
}
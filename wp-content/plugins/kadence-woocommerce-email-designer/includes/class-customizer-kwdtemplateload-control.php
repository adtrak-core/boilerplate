<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'WP_Customize_Kwdtemplateload_Control' ) ) {
	class WP_Customize_Kwdtemplateload_Control extends WP_Customize_Control {
		public $type = 'kwdtemplateload';


		public function render_content() {

			$name = 'kt-woomail-prebuilt-template';
			?>

			<div style="padding-bottom: 20px;">
				<span style="color:#0e9cd1"><strong>NEW!</strong></span>
				<h2 style="margin-top:0; padding: 5px 0;">Free Fluid Template</h2>
				<a href="https://www.kadencethemes.com/download-responsive-woocommerce-email-design/" target="_blank" style="background:#0e9cd1; font-weight:bold; text-decoration: none; color:white; padding:10px;">Download Here</a>
			</div>
			<span class="customize-control-title">
				<?php _e( 'Load Template', 'kadence-woocommerce-email-designer' ); ?>
			</span>
			<div class="kt-template-woomail-load-controls">
				<div id="input_<?php echo $this->id; ?>" class="image-radio-select">
				<?php foreach ( $this->choices as $value => $label ) : ?>
					<label class="<?php echo $this->id . $value; ?> image-radio-select-item" data-image-value="<?php echo esc_attr( $value ); ?>">
						<img src="<?php echo esc_url( KT_WOOMAIL_URL .  $label ); ?>" alt="<?php echo esc_attr( $value ); ?>" title="<?php echo esc_attr( $value ); ?>">
					</label>
				<?php endforeach; ?>
				</div>
				<input type="hidden" value="<?php echo esc_attr( $this->value() ); ?>" id="kt-woomail-prebuilt-template" name="kt-woomail-prebuilt-template">
				<?php wp_nonce_field( 'kt-woomail-importing-template', 'kt-woomail-import-template' ); ?>
			</div>
			<div class="kadence-woomail-loading"><?php _e( 'Loading and Saving...', 'kadence-woocommerce-email-designer' ); ?></div>
			<input type="button" class="button button-primary kadence-woomail-button" name="kadence-woomail-template-button" value="<?php esc_attr_e( 'Load Template', 'kadence-woocommerce-email-designer' ); ?>" />
			<?php
		}
	}
}
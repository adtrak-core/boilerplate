<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPLv3
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form method="post" action="options.php">
<?php 
	settings_fields( 'ppp_configurator_group' );
	do_settings_sections( 'ppp_configurator_group' );
?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'Expiration hours' , $this->plugin_slug ) ?></th>
					<td>
						<input id="ppp_configurator_expiration_hours" class="regular-text" type="text" value="<?php echo esc_html( get_option( 'ppp_configurator_expiration_hours' ) ) ?>" name="ppp_configurator_expiration_hours" />
						<p class="description"><?php echo _x( "Expiration of the preview link in hours (default = 48)", 'Explanation text for input field', $this->plugin_slug ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>

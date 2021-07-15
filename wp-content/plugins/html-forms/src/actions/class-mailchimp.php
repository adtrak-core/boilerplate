<?php

namespace HTML_Forms\Actions;

use HTML_Forms\Form;
use HTML_Forms\Submission;

class MailChimp extends Action {
	public $type  = 'mailchimp';
	public $label = 'MailChimp';

	public function __construct() {
		$this->label = __( 'MailChimp', 'html-forms' );
	}

	/**
	* @return array
	*/
	private function get_default_settings() {
		$defaults = array(
			'list_id' => '',
		);
		return $defaults;
	}

	/**
	* @param array $settings
	* @param string|int $index
	*/
	public function page_settings( $settings, $index ) {
		$settings  = array_merge( $this->get_default_settings(), $settings );
		$mailchimp = new \MC4WP_MailChimp();
		$lists     = $mailchimp->get_lists();

		if ( ! empty( $settings['list_id'] ) ) {
			  $selected_list = $mailchimp->get_list( $settings['list_id'] );
		}
		?>

		<?php if ( ! empty( $selected_list ) ) { ?>
		   <span class="hf-action-summary"><?php printf( __( 'Subscribe to %s', 'html-forms' ), $selected_list->name ); ?></span>
	<?php } ?>
	   <input type="hidden" name="form[settings][actions][<?php echo $index; ?>][type]" value="<?php echo $this->type; ?>" />
	   <table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e( 'List', 'html-forms' ); ?></th>
			<td>
				<?php if ( ! empty( $lists ) ) { ?>
					<select name="form[settings][actions][<?php echo $index; ?>][list_id]">
						<option value="" style="color: #AAA;" readonly><?php _e( 'Select MailChimp list', 'html-forms' ); ?></option>
						<?php
						foreach ( $lists as $list ) {
							$selected = $settings['list_id'] === $list->id ? 'selected' : '';
							echo sprintf( '<option value="%s" %s>%s</option>', $list->id, $selected, $list->name );
						}
						?>
					</select>
					<?php
				} else {
					echo '<p><a href="' . admin_url( 'admin.php?page=mailchimp-for-wp' ) . '">' . __( 'Please connect your MailChimp account first.', 'html-forms' ) . '</a></p>';
				}
				?>
			</td>

		</tr>
	   </table>
		<?php
	}

	public function process( array $settings, Submission $submission, Form $form ) {
		if ( empty( $settings['list_id'] ) ) {
			return;
		}

		$mailchimp_list_id = $settings['list_id'];
		$email_address     = '';

		// find email field
		foreach ( $submission->data as $field => $value ) {
			if ( is_email( $value ) ) {
				$email_address = $value;
			}
		}

		// bail if no email address found
		if ( empty( $email_address ) ) {
			return;
		}

		$merge_fields   = array();
		$merge_fields   = apply_filters( 'hf_mailchimp_action_merge_fields', $merge_fields, $submission, $form );
		$mailchimp_data = array(
			'merge_fields' => $merge_fields,
			'status'       => 'pending',
		);
		$mailchimp_data = apply_filters( 'hf_mailchimp_action_subscriber_data', $mailchimp_data, $submission, $form );

		// subscribe the email address to the selected list
		$mailchimp = new \MC4WP_MailChimp();
		$result    = $mailchimp->list_subscribe( $mailchimp_list_id, $email_address, $mailchimp_data );

		// if result failed, show error message
		$log  = mc4wp_get_debug_log();
		$name = sprintf( 'HTML Forms: %s', $form->title );
		if ( ! $result ) {
			if ( $mailchimp->get_error_code() == 214 ) {
				$log->warning( sprintf( '%s: %s is already subscribed to the selected list(s)', $name, $email_address ) );
			} else {
				$log->error( sprintf( '%s > Mailchimp API Error: %s', $name, $mailchimp->get_error_message() ) );
			}

			return;
		}

		$log->info( sprintf( '%s > Successfully subscribed %s', $name, $email_address ) );
	}
}

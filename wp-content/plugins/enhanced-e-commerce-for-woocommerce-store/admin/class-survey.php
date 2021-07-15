<?php
if ( ! class_exists( 'TVC_Survey' ) ) {
	
	class TVC_Survey {
		public $api_url = TVC_API_CALL_URL.'/customersurvey';
		public $name;
		public $plugin;
		protected $TVC_Admin_DB_Helper;
		protected $apiCustomerId;
		protected $subscriptionId;
		public function __construct( $name = '', $plugin = '' ){
			$this->name   = $name;
			$this->plugin = $plugin;
			if ( $this->is_dev_url() ) {
				return;
			}
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$this->apiCustomerId = $this->TVC_Admin_Helper->get_api_customer_id();
 			$this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();

			add_action( 'admin_print_scripts', array( $this, 'tvc_js'    ), 20 );
			add_action( 'admin_print_scripts', array( $this, 'tvc_css'   )     );
			add_action( 'admin_footer',        array( $this, 'tvc_modal' )     );
		}
		public function is_dev_url() {
			$url = network_site_url( '/' );
			$is_local_url = false;
			// Trim it up
			$url = strtolower( trim( $url ) );
			if ( false === strpos( $url, 'http://' ) && false === strpos( $url, 'https://' ) ) {
				$url = 'http://' . $url;
			}
			$url_parts = parse_url( $url );
			$host      = ! empty( $url_parts['host'] ) ? $url_parts['host'] : false;
			if ( ! empty( $url ) && ! empty( $host ) ) {
				if ( false !== ip2long( $host ) ) {
					if ( ! filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
						$is_local_url = true;
					}
				} else if ( 'localhost' === $host ) {
					$is_local_url = true;
				}

				$tlds_to_check = array( '.dev', '.local', ':8888' );
				foreach ( $tlds_to_check as $tld ) {
						if ( false !== strpos( $host, $tld ) ) {
							$is_local_url = true;
							continue;
						}

				}
				if ( substr_count( $host, '.' ) > 1 ) {
					$subdomains_to_check =  array( 'dev.', '*.staging.', 'beta.', 'test.' );
					foreach ( $subdomains_to_check as $subdomain ) {
						$subdomain = str_replace( '.', '(.)', $subdomain );
						$subdomain = str_replace( array( '*', '(.)' ), '(.*)', $subdomain );
						if ( preg_match( '/^(' . $subdomain . ')/', $host ) ) {
							$is_local_url = true;
							continue;
						}
					}
				}
			}
			return $is_local_url;
		}
		public function is_plugin_page() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( empty( $screen ) ) {
				return false;
			}
			return ( ! empty( $screen->id ) && in_array( $screen->id, array( 'plugins', 'plugins-network' ), true ) );
		}
		public function tvc_js() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}
			?>
			<script type="text/javascript">
			jQuery(function($){
				var $deactivateLink = $('#the-list').find('[data-slug="<?php echo $this->plugin; ?>"] span.deactivate a'),
					$overlay        = $('#ee-survey-<?php echo $this->plugin; ?>'),
					$form           = $overlay.find('form'),
					formOpen        = false;
				// Plugin listing table deactivate link.
				$deactivateLink.on('click', function(event) {
					event.preventDefault();
					$overlay.css('display', 'table');
					formOpen = true;
					$form.find('.ee-survey-option:first-of-type input[type=radio]').focus();
				});
				// Survey radio option selected.
				$form.on('change', 'input[type=radio]', function(event) {
					event.preventDefault();
					$form.find('input[type=text], .error').hide();
					$form.find('.ee-survey-option').removeClass('selected');
					$(this).closest('.ee-survey-option').addClass('selected').find('input[type=text]').show();
				});
				// Survey Skip & Deactivate.
				$form.on('click', '.ee-survey-deactivate', function(event) {
					event.preventDefault();
					var data = {
						action:'tvc_call_add_survey',
						customer_id:'<?php echo $this->apiCustomerId; ?>',
						subscription_id:'<?php echo $this->subscriptionId; ?>',
						radio_option_val: "skip",
						other_reason: "",
						site_url: '<?php echo esc_url( home_url() ); ?>',
						plugin_name: 'ee-woocommerce'
					}
					add_survey(data);
				});
				// Survey submit.
				$form.submit(function(event) {
					event.preventDefault();
					if (! $form.find('input[type=radio]:checked').val()) {
						$form.find('.ee-survey-footer').prepend('<span class="error"><?php echo esc_js( __( 'Please select an option', 'google-analytics-for-wordpress' ) ); ?></span>');
						return;
					}
					var data = {
						action:'tvc_call_add_survey',
						customer_id:'<?php echo $this->apiCustomerId; ?>',
						subscription_id:'<?php echo $this->subscriptionId; ?>',
						radio_option_val: $form.find('.selected input[type=radio]').val(),
						other_reason: $form.find('.selected input[type=text]').val(),
						site_url: '<?php echo esc_url( home_url() ); ?>',
						plugin_name: 'ee-woocommerce'
					}
					add_survey(data);
					/*var submitSurvey = $.post('<?php echo $this->api_url; ?>', data);
					submitSurvey.always(function() {
						$(".ee-survey-modal").hide(100);
						location.href = $deactivateLink.attr('href');
					});*/
				});
				// Exit key closes survey when open.
				$(document).keyup(function(event) {
					if (27 === event.keyCode && formOpen) {
						$overlay.hide();
						formOpen = false;
						$deactivateLink.focus();
					}
				});
				function add_survey(data){
					$.ajax({
		        type: "POST",
		        dataType: "json",
		        url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
		        data: data,
		        beforeSend: function(){
		        	//$('.ee-survey-submit').html("Thanks..");
		        	$('.ee-survey-submit').prop('disabled', true);
		        	$('.ee-survey-deactivate').hide();
		        },
		        success: function(response){
		          $(".ee-survey-modal").hide();
							location.href = $deactivateLink.attr('href');		          
		        }
		      });
				}
			});
			</script>
			<?php
		}
		public function tvc_css() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}
			?>
			<style type="text/css">
			.ee-survey-modal {
				width: 100%;
				height: 100%;
				display: none;
				table-layout: fixed;
				position: fixed;
				z-index: 9999;				
				text-align: center;
				font-size: 14px;
				top: 0;
				left: 0;
				background: rgba(0,0,0,0.8);
			}
			.ee-survey-wrap {
				display: table-cell;
				vertical-align: middle;
			}
			.ee-survey {
				background-color: #fff;
				padding: 32px;
				max-width: 540px;
				margin: 0 auto;				
				text-align: left;
				border-radius: 40px;
			}
			.ee-survey .error {
				display: block;
				color: red;
				margin: 0 0 10px 0;
			}
			.ee-survey-title {
				display: block;
				font-size: 18px;
				font-weight: 700;
				text-transform: uppercase;
				border-bottom: 1px solid #ddd;
				padding: 0 0 15px 0;
				margin: 0 0 15px 0;
			}
			.ee-survey-title span {
				color: #999;
				margin-right: 10px;
			}
			.ee-survey-desc {
				display: block;
				font-weight: 600;
				margin: 0 0 15px 0;
			}
			.ee-survey-option {
				margin: 0 0 10px 0;
			}
			.ee-survey-option-input {
				margin-right: 10px !important;
			}
			.ee-survey-option-details {
				display: none;
				width: 90%;
				margin: 10px 0 0 30px;
			}
			.ee-survey-footer {
				margin-top: 15px;
			}
			.ee-survey-deactivate {
				font-size: 13px;
		    color: #ccc;
		    text-decoration: none;		    
		    margin-top: 7px;
		    float: right;
		    position: relative;
		    display: inline-block;
			}
			.ee-survey-wrap .dashicons{
				font-size: 24px;
    color: #3C434A;
			}
			</style>
			<?php
		}
		public function tvc_modal() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}

			$options = array(
				1 => array(
					"title"   => esc_html__("No longer need the plugin","enhanced-e-commerce-for-woocommerce-store"),
				),
				2 => array(
					'title'   => esc_html__("Switching to a different plugin","enhanced-e-commerce-for-woocommerce-store"),
					'details' => esc_html__( 'Please share which plugin', 'google-analytics-for-wordpress' ),
				),
				3 => array(
					'title'   => esc_html__("Couldn't get the plugin to work","enhanced-e-commerce-for-woocommerce-store"),
				),
				4 => array(
					'title'   => esc_html__("It's a temporary deactivation","enhanced-e-commerce-for-woocommerce-store"),
				),
				5 => array(
					'title'   => esc_html__("Other","enhanced-e-commerce-for-woocommerce-store"),
					'details' => esc_html__( 'Please share the reason', 'google-analytics-for-wordpress' ),
				),
			);
			?>
			<div class="ee-survey-modal" id="ee-survey-<?php echo $this->plugin; ?>">
				<div class="ee-survey-wrap">
					<form class="ee-survey" method="post">
						<span class="ee-survey-title"><span class="dashicons dashicons-admin-customizer"></span><?php echo ' ' . esc_html__( 'Quick Feedback', 'google-analytics-for-wordpress' ); ?></span>
						<span class="ee-survey-desc">
							<?php
							// Translators: Placeholder for the plugin name.
							echo sprintf( esc_html__('If you have a moment, please share why you are deactivating %s:', 'google-analytics-for-wordpress' ), $this->name );
							?>
						</span>
						<div class="ee-survey-options">
							<?php foreach ( $options as $id => $option ) : 
								//$slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $option['title']);
								$slug = sanitize_title($option['title']); ?>
							<div class="ee-survey-option">
								<label for="ee-survey-option-<?php echo $this->plugin; ?>-<?php echo $id; ?>" class="ee-survey-option-label">
									<input id="ee-survey-option-<?php echo $this->plugin; ?>-<?php echo $id; ?>" class="ee-survey-option-input" type="radio" name="code" value="<?php echo $slug; ?>" />
									<span class="ee-survey-option-reason"><?php echo $option['title']; ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
								<input class="ee-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>" />
								<?php endif; ?>
							</div>
							<?php endforeach; ?>
						</div>
						<div class="ee-survey-footer">
							<button type="submit" class="ee-survey-submit button button-primary button-large">
								<?php
								// Translators: Adds an ampersand.
								echo sprintf( esc_html__('Submit %s Deactivate', 'google-analytics-for-wordpress' ), '&amp;' );
								?>
							</button>
							<a href="#" class="ee-survey-deactivate">
								<?php
								// Translators: Adds an ampersand.
								echo sprintf( esc_html__('Skip %s Deactivate', 'google-analytics-for-wordpress' ), '&amp;' );
								?>
							</a>
						</div>
					</form>
				</div>
			</div>
			<?php
		}
	}
} 
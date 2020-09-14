<?php
	class SinglePreloadWPFC{
		public static $id = 0;
		public static $urls = array();

		public static function init(){
			SinglePreloadWPFC::set_id();
			SinglePreloadWPFC::set_urls();
			SinglePreloadWPFC::set_urls_with_terms();
		}

		public static function set_id(){
			if(isset($_GET["post"]) && $_GET["post"]){
				static::$id = esc_sql($_GET["post"]);

				if(get_post_status(static::$id) != "publish"){
					static::$id = 0;
				}
			}
		}

		public static function create_cache(){
			$res = $GLOBALS["wp_fastest_cache"]->wpfc_remote_get($_GET["url"], $_GET["user_agent"]);

			if($res){
				die("true");
			}
		}

		public static function is_mobile_active(){
			if(isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheMobile) && isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheMobileTheme)){
				return true;
			}else{
				return false;
			}
		}

		public static function set_term_urls($term_taxonomy_id){
			$term = get_term_by("term_taxonomy_id", $term_taxonomy_id);

			if($term && !is_wp_error($term)){
				$url = get_term_link($term->term_id, $term->taxonomy);

				array_push(static::$urls, array("url" => $url, "user-agent" => "WP Fastest Cache Preload Bot"));

				if(self::is_mobile_active()){
					array_push(static::$urls, array("url" => $url, "user-agent" => "WP Fastest Cache Preload iPhone Mobile Bot"));
				}

				if($term->parent > 0){
					$parent = get_term_by("id", $term->parent, $term->taxonomy);

					static::set_term_urls($parent->term_taxonomy_id);
				}
			}
		}

		public static function set_urls_with_terms(){
			global $wpdb;
			$terms = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."term_relationships` WHERE `object_id`=".static::$id, ARRAY_A);

			foreach ($terms as $term_key => $term_val){
				static::set_term_urls($term_val["term_taxonomy_id"]);
			}
		}

		public static function set_urls(){
			if(static::$id){
				$permalink = get_permalink(static::$id);

				array_push(static::$urls, array("url" => $permalink, "user-agent" => "WP Fastest Cache Preload Bot"));

				if(self::is_mobile_active()){
					array_push(static::$urls, array("url" => $permalink, "user-agent" => "WP Fastest Cache Preload iPhone Mobile Bot"));
				}
			}
		}

		public static function put_inline_js(){
			$screen = get_current_screen();

			if($screen->parent_base == "edit" && $screen->base == "post"){
				?>
			    <div id="wpfc-single-preload" class="notice notice-info is-dismissible" style="display: none;">
			        <p id="wpfc-single-preload-info"><?php _e('Cache is generated for this content', 'wp-fastest-cache');?><label style="display: none;" id="wpfc-single-preload-error">0</label><label id="wpfc-single-preload-process" style="padding-left: 5px;"><span>0</span>/<span><?php echo count(static::$urls); ?></span></label></p>
			        
			        <script type="text/javascript">
			        	var WpfcSinglePreload = {
			        		error_message: "",
			        		init: function(){},
			        		change_status: function(){
			        			var type = "";
			        			var cached_number = parseInt(jQuery("#wpfc-single-preload-process span").first().text());
			        			var total = parseInt(jQuery("#wpfc-single-preload-process span").last().text());
			        			var error_number = parseInt(jQuery("#wpfc-single-preload-error").text());

			        			if(cached_number == total){
			        				type = "success";
			        			}

			        			if((error_number + cached_number) == total){
			        				if(error_number > cached_number){
				        				type = "error";
				        			}else{
				        				type = "success";
				        			}
			        			}

			        			if(type){
					        		var class_name = jQuery("#wpfc-single-preload").attr("class");
					        		class_name = class_name.replace("notice-info", "notice-" + type);
					        		jQuery("#wpfc-single-preload").attr("class", class_name);

					        		if(type == "success"){
					        			jQuery("#wpfc-single-preload p").text("<?php _e('Cache has been generated for this content successfully', 'wp-fastest-cache');?>");
					        		}else{
					        			if(this.error_message){
					        				this.error_message = "<?php _e('Cache has NOT been generated for this content successfully', 'wp-fastest-cache');?>" + "<br>" + "<?php _e('Reason:', 'wp-fastest-cache');?>" + " " + this.error_message;
					        				jQuery("#wpfc-single-preload p").html(this.error_message);
					        			}
					        		}
			        			}
			        		},
			        		increase_error: function(){
			        			var error_number = jQuery("#wpfc-single-preload-error").text();
			        			error_number = parseInt(error_number) + 1;
			        			jQuery("#wpfc-single-preload-error").text(error_number);

			        			this.change_status();
			        		},
			        		create_cache: function(url, user_agent){
			        			var self = this;
			        			jQuery("#wpfc-single-preload").show();

				        		jQuery.ajax({
									type: 'GET',
									url: ajaxurl,
									data: {"action": "wpfc_preload_single", "url": url, "user_agent": user_agent},
									dataType: "html",
									timeout: 10000,
									cache: false, 
									success: function(data){
										if(data == "true"){
											var number = jQuery("#wpfc-single-preload-process span").first().text();
											number = parseInt(number) + 1;

											jQuery("#wpfc-single-preload-process span").first().text(number);
										}else{
											self.error_message = data;
											WpfcSinglePreload.increase_error();
										}

										self.change_status();
									},
									error: function(error){
										self.error_message = error.statusText;
										WpfcSinglePreload.increase_error();
									}
								});
			        		}
			        	};


			        	jQuery(document).ready(function(){
			        		if(jQuery("#message").find("a").attr("href")){
					        	WpfcSinglePreload.init();

					        	<?php
					        		foreach (self::$urls as $key => $value) {
					        			?>
					        			setTimeout(function(){
					        				WpfcSinglePreload.create_cache("<?php echo $value["url"]; ?>", "<?php echo $value["user-agent"]; ?>");
										}, <?php echo $key*500;?>);
					        			<?php
					        		}
					        	?>
			        		}
			        	});
			        </script>
			    </div>
			    <?php
			}
		}
	}
?>
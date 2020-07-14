<?php

if ( $this instanceof \PGMB\Components\PostEditor ) {
    ?>
    <div class="mbp-post-form-container<?php 
    if ( $this->is_ajax_enabled() ) {
        ?> hidden<?php 
    }
    ?>">
        <div class="nav-tab-wrapper current">
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-alert-field'
               data-topic='ALERT'><span
                        class="dashicons dashicons-sos"></span> <?php 
    _e( 'COVID-19 update', 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab nav-tab-active mbp-tab-default" data-fields='mbp-whatsnew-field'
               data-topic='STANDARD'><span
                    class="dashicons dashicons-megaphone"></span> <?php 
    _e( "What's New", 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-event-field' data-topic='EVENT'><span
                    class="dashicons dashicons-calendar"></span> <?php 
    _e( "Event", 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-offer-field' data-topic='OFFER'><span
                    class="dashicons dashicons-tag"></span> <?php 
    _e( "Offer", 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-product-field' data-topic='PRODUCT'><span
                    class="dashicons dashicons-cart"></span> <?php 
    _e( "Product", 'post-to-google-my-business' );
    ?>
            </a>
        </div>

        <div class='mbp-tabs-container'>

            <fieldset id='mbp-post-data'>
                <!--			<input type='hidden' name='mbp_attachment_type' class='mbp-hidden' value='PHOTO' />-->
                <?php 
    
    if ( !$this->is_ajax_enabled() ) {
        ?>
                    <input type="hidden" class="mbp-hidden mbp-attachment-type"
                           value="<?php 
        echo  $this->fields['mbp_attachment_type'] ;
        ?>"/>
                    <input type="hidden" class="mbp-hidden mbp-post-attachment"
                           value="<?php 
        echo  $this->fields['mbp_post_attachment'] ;
        ?>"/>
                <?php 
    }
    
    ?>

                <input type="hidden" name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_alert_type]" value="<?php 
    echo  $this->fields['mbp_alert_type'] ;
    ?>" />

                <input type='hidden' name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_topic_type]'
                       class='mbp-hidden mbp-topic-type' value="<?php 
    echo  $this->fields['mbp_topic_type'] ;
    ?>"/>

                <table class="form-table mbp-fields">


                    <!-- What's new fields -->

                    <tr class='mbp-whatsnew-field mbp-event-field mbp-offer-field'
                        id="post-image-container"> <!-- mbp-product-field -->
                        <th><label
                                for='post_image'><?php 
    _e( 'Post image/video', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>

                            <!--							<input type="text" name="mbp_post_attachment" id="meta-image" class="meta_image" />-->
                            <div class="mediaupload_selector">

                            </div>
                            <br/><span
                                class='description'><?php 
    _e( 'JPG or PNG, 720x540px minimum size', 'post-to-google-my-business' );
    ?></span>
                        </td>
                    </tr>

                    <tr class='mbp-whatsnew-field mbp-alert-field mbp-event-field mbp-offer-field'
                        id='post-text-container'>
                        <th><label for='post_text'><?php 
    _e( 'Post text', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>
                            <textarea id='post_text' name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_post_text]'
                                      class='mbp-required' rows="8"
                                      style='width:100%'><?php 
    echo  $this->fields['mbp_post_text'] ;
    ?></textarea>
                            <div
                                class="mbp-text-details"><?php 
    _e( 'Characters:', 'post-to-google-my-business' );
    ?>
                                <span class="mbp-character-count">0</span>/1500
                                - <?php 
    _e( 'Word count:', 'post-to-google-my-business' );
    ?> <span
                                    class="mbp-word-count">0</span></div>
                            <br/><span
                                class='description'><?php 
    _e( 'The text that should appear on your post. Recommended 150-300 characters. 80 characters show in the Google Search results. 1500 characters maximum.', 'post-to-google-my-business' );
    ?></span>
                        </td>
                    </tr>


                        <!-- Event fields -->
                        <tr class='mbp-event-field hidden' id='event-title-container'>
                            <th><label
                                    for='event_title'><?php 
    _e( 'Event title', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='event_title' class='mbp-required'
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_event_title]'
                                       value="<?php 
    echo  $this->fields['mbp_event_title'] ;
    ?>"/>
                            </td>
                        </tr>
                        <tr class='mbp-event-field mbp-offer-field hidden'>
                            <th></th>
                            <td>
                                <label><input type="checkbox" name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_event_all_day]" id="mbp_event_all_day" /> <?php 
    esc_html_e( 'All day event (time will be ignored)', 'post-to-google-my-business' );
    ?></label>
                            </td>
                        </tr>
                        <tr class='mbp-event-field mbp-offer-field hidden' id='event-start-date-container'>
                            <th><label
                                    for='event_start_date'><?php 
    _e( 'Start date', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='event_start_date'
                                       class='mbp-required mbp-validate-date'
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_event_start_date]'
                                       value="<?php 
    echo  $this->fields['mbp_event_start_date'] ;
    ?>"/>
                                <span id="event_start_date_validator" class="mbp-validated-date-display"></span>
                            </td>
                        </tr>
                        <tr class='mbp-event-field mbp-offer-field hidden' id='event-end-date-container'>
                            <th><label
                                    for='event_end_date'><?php 
    _e( 'End date', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='event_end_date'
                                       class='mbp-required mbp-validate-date'
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_event_end_date]'
                                       value="<?php 
    echo  $this->fields['mbp_event_end_date'] ;
    ?>"/>
                                <span id="event_end_date_validator" class="mbp-validated-date-display"></span>
                            </td>
                        </tr>


                        <!-- Offer fields -->
                        <tr class='mbp-offer-field hidden' id='offer-title-container'>
                            <th><label
                                    for='offer_title'><?php 
    _e( 'Offer title', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_title' class='mbp-required'
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_offer_title]'
                                       value="<?php 
    echo  $this->fields['mbp_offer_title'] ;
    ?>"/>
                                <br/><span
                                    class='description'><?php 
    _e( 'Example: 20% off in store or online', 'post-to-google-my-business' );
    ?></span>
                            </td>
                        </tr>
                        <tr class='mbp-offer-field hidden' id='offer-coupon-container'>
                            <th><label
                                    for='offer_coupon'><?php 
    _e( 'Coupon code (optional)', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_coupon' class=''
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_offer_coupon]'
                                       value="<?php 
    echo  $this->fields['mbp_offer_coupon'] ;
    ?>"/>
                            </td>
                        </tr>
                        <tr class='mbp-offer-field hidden' id='offer-redeemlink-container'>
                            <th><label
                                    for='offer_redeemlink'><?php 
    _e( 'Link to redeem offer (optional)', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_redeemlink' class=''
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_offer_redeemlink]'
                                       value="<?php 
    echo  $this->fields['mbp_offer_redeemlink'] ;
    ?>"/>
                            </td>
                        </tr>
                        <tr class='mbp-offer-field hidden' id='offer-terms-container'>
                            <th><label
                                    for='offer_terms'><?php 
    _e( 'Terms and conditions (optional)', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_terms' class=''
                                       name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_offer_terms]'
                                       value="<?php 
    echo  $this->fields['mbp_offer_terms'] ;
    ?>"/>
                            </td>
                        </tr>


                        <!-- Product fields -->
                        <tr class='mbp-product-field hidden' id='product-name-container'>
                            <td colspan="2">
                                <?php 
    _e( 'The ability to create Product posts has been (temporarily?) removed from the Google My Business API.', 'post-to-google-my-business' );
    ?><br /><br />
                                <strong>


                                <?php 
    printf( __( 'Check out %s for (auto-)publishing your WooCommerce products on Google My Business.', 'post-to-google-my-business' ), sprintf( '<a href="https://tycoonmedia.net/blog/auto-publish-woocommerce-products-to-google-my-business/" target="_blank">%s</a>', __( 'this workaround', 'post-to-google-my-business' ) ) );
    ?>
                                </strong>
                            </td>
                        </tr>
                        <?php 
    /*
    <tr class='mbp-product-field hidden' id='product-name-container'>
        <th><label for='product_name'><?php _e('Product name', 'post-to-google-my-business'); ?></label></th>
        <td>
            <input type='text' id='product_name' class='mbp-required' name='mbp_product_name' placeholder='<?php esc_html_e('Product Name', 'post-to-google-my-business'); ?>' />
        </td>
    </tr>
    <tr class='mbp-product-field hidden' id='product-price-container'>
        <th><label for='product_minprice'><?php _e('Price', 'post-to-google-my-business'); ?></label></th>
        <td>
            <!--<input type='number' id='mbp_product_price' class='mbp-required' name='mbp_product_price' placeholder='Price' step="0.01" /> -->
    
            <input type='number' id='product_minprice' class='' name='mbp_product_minprice' placeholder='<?php esc_html_e('Price', 'post-to-google-my-business'); ?>' step="0.01"/>
            <input type='number' id='product_maxprice' class='hidden' name='mbp_product_maxprice' placeholder='<?php esc_html_e('Max', 'post-to-google-my-business'); ?>' step="0.01" />
    
            <br />
            <label><input type='checkbox' name='mbp_product_pricerange' id='mbp_product_pricerange' value='1' /> <?php _e('Range', 'post-to-google-my-business'); ?></label>
            <br /><span class='description'><?php _e('Format: 123.45', 'post-to-google-my-business'); ?></span>
        </td>
    </tr>
    <tr class='mbp-product-field hidden' id='product-currency-container'>
        <th><label for='product_currency'><?php _e('Currency', 'post-to-google-my-business'); ?></label></th>
        <td>
            <select name='mbp_product_currency' id='product_currency'>
                <?php echo $this->get_currency_options__premium_only(new MBP_Currency_Codes(), 'USD'); ?>
            </select>
        </td>
    </tr>
    */
    ?>
                        <!--
                <tr class='mbp-product-field hidden' id='product-details-container'>
                    <th><label for='product_details'><?php 
    esc_html_e( 'Details', 'post-to-google-my-business' );
    ?></label></th>
                    <td>
                        <textarea id='product_details' name='mbp_product_details' rows="8" cols="100" style='width:100%' class='mbp-required'></textarea>
                        <br /><span class='description'><?php 
    esc_html_e( 'The text that should appear on your post. Recommended 150-300 characters. 80 characters show in the SERPS. 1500 characters maximum.', 'post-to-google-my-business' );
    ?></span>
                    </td>
                </tr>
                -->

                    <!-- Button field -->
                    <tr class='mbp-whatsnew-field mbp-alert-field mbp-event-field mbp-offer-field'>
                        <!-- mbp-product-field -->
                        <th><label
                                for='post_text'><?php 
    esc_html_e( 'Button', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>
                            <label><input type='checkbox' name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_button]'
                                          id='mbp_button'
                                          value='1' <?php 
    checked( $this->fields['mbp_button'] );
    ?> /> <?php 
    _e( 'Add a button', 'post-to-google-my-business' );
    ?>
                            </label>
                        </td>
                    </tr>
                    <tr class='mbp-button-settings hidden'>
                        <th><?php 
    _e( 'Button type', 'post-to-google-my-business' );
    ?></th>
                        <td>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="BOOK" <?php 
    checked( $this->fields['mbp_button_type'], "BOOK" );
    ?>> <?php 
    _e( 'Book', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="ORDER"<?php 
    checked( $this->fields['mbp_button_type'], "ORDER" );
    ?>> <?php 
    _e( 'Order', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="SHOP"<?php 
    checked( $this->fields['mbp_button_type'], "SHOP" );
    ?>> <?php 
    _e( 'Shop', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="LEARN_MORE"<?php 
    checked( $this->fields['mbp_button_type'], "LEARN_MORE" );
    ?>> <?php 
    _e( 'Learn more', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="SIGN_UP"<?php 
    checked( $this->fields['mbp_button_type'], "SIGN_UP" );
    ?>> <?php 
    _e( 'Sign up', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="GET_OFFER"<?php 
    checked( $this->fields['mbp_button_type'], "GET_OFFER" );
    ?>> <?php 
    _e( 'Get offer', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <label><input class="mbp-button-type" type="radio"
                                          name="<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_type]"
                                          value="CALL"<?php 
    checked( $this->fields['mbp_button_type'], "CALL" );
    ?>> <?php 
    _e( 'Call now (uses primary phone number of business)', 'post-to-google-my-business' );
    ?>
                            </label><br/>
                            <br/><span
                                class='description'><?php 
    _e( 'The text that should appear on your button', 'post-to-google-my-business' );
    ?></span>
                        </td>
                    </tr>
                    <tr class='mbp-button-settings mbp-button-url hidden'>
                        <th><label
                                for='button_url'><?php 
    _e( 'Alternative button URL', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>
                            <input type='text' id='button_url'
                                   name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_button_url]' style='width:100%'
                                   data-default="%post_permalink%"
                                   value="<?php 
    echo  $this->fields['mbp_button_url'] ;
    ?>"/>
                            <br/><span
                                class='description'><?php 
    _e( 'Optional. Where the user should go when clicking the button. Leave at default to send them to your newly created WordPress post.', 'post-to-google-my-business' );
    ?></span>
                        </td>
                    </tr>

                </table>


                <a href='#'
                   class='mbp-toggle-advanced'><?php 
    _e( 'Advanced post settings', 'post-to-google-my-business' );
    ?>
                    &darr;</a>
                <div class='mbp-advanced-post-settings hidden'>
                    <table class="form-table mbp-fields">
                        <tr class='mbp-whatsnew-field mbp-product-field mbp-offer-field mbp-event-field'>
                            <th>
                                <?php 
    _e( 'Image settings', 'post-to-google-my-business' );
    ?>
                            </th>
                            <td>
                                <label><input type='checkbox'
                                              name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_content_image]'
                                              id='mbp_content_image' value='1'
                                              data-default="" <?php 
    checked( $this->fields['mbp_content_image'] );
    ?> /> <?php 
    _e( 'Fetch image from post content', 'post-to-google-my-business' );
    ?>
                                </label>
                                <br/><span
                                    class='description'><?php 
    _e( 'Try to get an image from the post content (when no custom image is set). This takes priority over the featured image.', 'post-to-google-my-business' );
    ?></span><br/><br/>
                                <label><input type='checkbox'
                                              name='<?php 
    echo  $this->fieldName ;
    ?>[mbp_featured_image]'
                                              id='mbp_featured_image' value='1'
                                              data-default="" <?php 
    checked( $this->fields['mbp_featured_image'] );
    ?> /> <?php 
    _e( 'Use Featured Image as GMB post image', 'post-to-google-my-business' );
    ?>
                                </label>
                                <br/><span
                                    class='description'><?php 
    _e( 'Use the Featured Image as GMB Post image (when no custom image is set)', 'post-to-google-my-business' );
    ?></span>
                            </td>
                        </tr>
                    </table>
                    <?php 
    
    if ( mbp_fs()->is_not_paying() ) {
        ?>
                        <br/>
                        <?php 
        _e( 'Schedule your Google My Business posts, automatically repost them at specified interval, and pick one or more locations to post to. And many more features!' );
        ?>
                        <br/><br/>
                        <a target="_blank" class='button-primary'
                           href="<?php 
        echo  mbp_fs()->get_upgrade_url() ;
        ?>"><?php 
        _e( 'Upgrade now!', 'post-to-google-my-business' );
        ?></a>
                        <br/><br/>
                    <?php 
    }
    
    ?>
                    <?php 
    ?>
                </div>
                <br/><br/>
                <?php 
    
    if ( $this->is_ajax_enabled() ) {
        ?>
                    <div class="button-group">
                        <a href='#' class='button button-secondary'
                           id='mbp-cancel-post'><?php 
        _e( 'Cancel', 'post-to-google-my-business' );
        ?></a>
                        <button class='button button-secondary'
                                id='mbp-draft-post'><?php 
        _e( 'Save draft', 'post-to-google-my-business' );
        ?></button>
                        <!--                <button class='button button-secondary' id='mbp-preview-post'>-->
                        <?php 
        //_e('Preview', 'post-to-google-my-business');
        ?><!--</button>-->
                        <button class='button button-primary'
                                id='mbp-publish-post'><?php 
        _e( 'Publish Now', 'post-to-google-my-business' );
        ?></button>
                    </div>
                <?php 
    }
    
    ?>
            </fieldset>
        </div>
    </div>
<?php 
}

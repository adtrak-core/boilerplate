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

                    <tr class='mbp-whatsnew-field<?php 
    ?>'
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

                    <tr class='mbp-whatsnew-field mbp-alert-field<?php 
    ?>'
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

                    <?php 
    ?>
                        <tr class='mbp-product-field mbp-offer-field mbp-event-field hidden'>
                            <td colspan='2'>
                                <p><?php 
    _e( 'Have more people engage with your events, products or offers.', 'post-to-google-my-business' );
    ?>
                                    <a target="_blank"
                                       href="<?php 
    echo  mbp_fs()->get_upgrade_url() ;
    ?>"><?php 
    _e( 'Upgrade to premium', 'post-to-google-my-business' );
    ?></a> <?php 
    _e( 'and start creating your Event, Offer and Product posts right from the WordPress dashboard!', 'post-to-google-my-business' );
    ?>
                                </p><br/>
                                <a target="_blank" class='button-primary'
                                   href="<?php 
    echo  mbp_fs()->get_upgrade_url() ;
    ?>"><?php 
    _e( 'Upgrade now!', 'post-to-google-my-business' );
    ?></a>
                            </td>
                        </tr>
                    <?php 
    ?>

                    <!-- Button field -->
                    <tr class='mbp-whatsnew-field mbp-alert-field<?php 
    ?>'>
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

<?php if($this instanceof MBP_Metabox) : ?>
    <div class='mbp-error-notice'>

    </div>
    <div class='mbp-table-head'>
        <?php if($this->is_autopost_enabled()) : ?>
            <input type="hidden" value="1" name="mbp_wp_post" /> <!-- Hidden value so we can determine if the page was submitted without checking the checkbox -->
            <div class="button-group">
                <a href="#" class="button button-secondary"  id='mbp-edit-post-template'><?php _e('Edit auto-post template', 'post-to-google-my-business'); ?></a>
                <a href='#' class='button button-primary' id='mbp-new-post'>+ <?php _e('New GMB Post', 'post-to-google-my-business'); ?></a>
            </div>
        <?php else: ?>
            <a href='#' class='button button-primary' id='mbp-new-post'>+ <?php _e('New GMB Post', 'post-to-google-my-business'); ?></a>
        <?php endif; ?>
    </div>

    <?php echo $this->post_editor->generate(); ?>

    <table class="widefat fixed striped mbp-existing-posts" cellspacing="0">
        <thead>
            <tr>
                <!--<th id="cb" class="manage-column column-cb check-column" scope="col"></th>-->
                <th class="manage-column column-posttype" scope="col"><?php _e('Post type', 'post-to-google-my-business'); ?></th>
                <th class="manage-column column-postdate" scope="col"><?php _e('Publish date', 'post-to-google-my-business'); ?></th>
                <th class="manage-column column-postcreated" scope="col"><?php _e('Created', 'post-to-google-my-business'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $this->get_existing_posts($post->ID); ?>
        </tbody>
    </table>
    <br />

    <div id="mbp-created-post-dialog" class="hidden">
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <th scope="col" class="manage-column column-primary">
                    <span><?php _e('Location', 'post-to-google-my-business'); ?></span>
                </th>
                <th scope="col" class="manage-column"><?php _e('Status', 'post-to-google-my-business'); ?></th>
            </tr>
            </thead>
            <tbody id="mbp-created-post-table">

            </tbody>
        </table>
    </div>
<?php endif; ?>

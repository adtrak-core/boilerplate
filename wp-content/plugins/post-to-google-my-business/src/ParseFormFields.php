<?php

namespace PGMB;

use  PGMB\Google\Date ;
use  PGMB\Google\LocalPost ;
use  PGMB\Vendor\Cron\CronExpression ;
use  PGMB\Vendor\Html2Text\Html2Text ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone ;
use  DateTime ;
class ParseFormFields
{
    private  $form_fields ;
    public function __construct( $form_fields )
    {
        if ( !is_array( $form_fields ) ) {
            throw new \InvalidArgumentException( 'ParseFormFields expects Form Fields array' );
        }
        $this->form_fields = $form_fields;
    }
    
    /**
     * Get DateTime object representing the when a post will be first published
     *
     * @return bool|DateTime|false DateTime when the post is first published, or false when the post isn't scheduled
     * @throws \Exception Invalid DateTime
     */
    public function getPublishDateTime()
    {
        return false;
    }
    
    /**
     * Parse the form fields and return a LocalPost object
     *
     * @param $parent_post_id
     *
     * @return LocalPost
     * @throws \Exception
     */
    public function getLocalPost( $parent_post_id )
    {
        if ( !is_numeric( $parent_post_id ) ) {
            throw new \InvalidArgumentException( 'Parent Post ID required for placeholder parsing' );
        }
        $summary = stripslashes( $this->form_fields['mbp_post_text'] );
        if ( mbp_fs()->is_plan_or_trial( 'business' ) ) {
            $summary = \MBP_Spintax::Parse( $summary );
        }
        $summary = $this->parse_placeholder_variables( $summary, $parent_post_id );
        $summary = mb_strimwidth(
            $summary,
            0,
            1500,
            "..."
        );
        $localPost = new LocalPost( get_bloginfo( 'language' ), $summary, $this->form_fields['mbp_topic_type'] );
        //Add image/video
        $mediaItem = $this->get_media_item( $parent_post_id );
        if ( $mediaItem ) {
            $localPost->addMediaItem( $mediaItem );
        }
        // mbp_content_image mbp_featured_image
        //Add button
        
        if ( isset( $this->form_fields['mbp_button'] ) && $this->form_fields['mbp_button'] ) {
            $buttonURL = $this->parse_placeholder_variables( $this->form_fields['mbp_button_url'], $parent_post_id );
            $callToAction = new \PGMB\Google\CallToAction( $this->form_fields['mbp_button_type'], $buttonURL );
            $localPost->addCallToAction( $callToAction );
        }
        
        return $localPost;
    }
    
    public function get_media_item( $parent_post_id )
    {
        
        if ( !empty($this->form_fields['mbp_post_attachment']) ) {
            return new \PGMB\Google\MediaItem( $this->form_fields['mbp_attachment_type'], $this->form_fields['mbp_post_attachment'] );
        } elseif ( isset( $this->form_fields['mbp_content_image'] ) && $this->form_fields['mbp_content_image'] && ($image_url = $this->get_content_image( $parent_post_id )) ) {
            return new \PGMB\Google\MediaItem( 'PHOTO', $image_url );
        } elseif ( isset( $this->form_fields['mbp_featured_image'] ) && $this->form_fields['mbp_featured_image'] && ($image_url = get_the_post_thumbnail_url( $parent_post_id, 'large' )) ) {
            return new \PGMB\Google\MediaItem( 'PHOTO', $image_url );
        }
        
        return false;
    }
    
    public function get_content_image( $post_id )
    {
        $images = get_attached_media( 'image', $post_id );
        if ( !($image = reset( $images )) ) {
            return false;
        }
        $image_details = wp_get_attachment_image_src( $image->ID, 'large' );
        return reset( $image_details );
        //Return the first item in the array (which is the url)
    }
    
    /**
     * Get array of locations to post to. Return default location if nothing is selected
     *
     * @param $default_location
     *
     * @return array Locations to post to
     */
    public function getLocations( $default_location )
    {
        if ( !isset( $this->form_fields['mbp_selected_location'] ) ) {
            return [ $default_location ];
        }
        
        if ( !is_array( $this->form_fields['mbp_selected_location'] ) ) {
            return [ $this->form_fields['mbp_selected_location'] ];
        } elseif ( is_array( $this->form_fields['mbp_selected_location'] ) ) {
            return $this->form_fields['mbp_selected_location'];
        }
        
        throw new \UnexpectedValueException( "Could not parse post locations" );
    }
    
    public function parse_placeholder_variables( $text, $post_id )
    {
        $post = get_post( $post_id );
        $variables = array();
        foreach ( $post as $key => $value ) {
            $variables['%' . $key . '%'] = $value;
        }
        $variables['%post_content%'] = $this->parse_post_content( $variables['%post_content%'] );
        $variables['%post_permalink%'] = get_permalink( $post_id );
        //User info
        $user_variables = array(
            'aim',
            'description',
            'display_name',
            'first_name',
            'jabber',
            'last_name',
            'nickname',
            'user_email',
            'user_nicename',
            'user_url',
            'yim'
        );
        foreach ( $user_variables as $variable ) {
            $variables['%author_' . $variable . '%'] = get_the_author_meta( $variable, $post->post_author );
        }
        $site_variables = array(
            'name',
            'description',
            'url',
            'pingback_url',
            'atom_url',
            'rdf_url',
            'rss_url',
            'rss2_url',
            'comments_atom_url',
            'comments_rss2_url'
        );
        foreach ( $site_variables as $variable ) {
            $variables['%site_' . $variable . '%'] = get_bloginfo( $variable );
        }
        $variables = apply_filters( 'mbp_placeholder_variables', $variables );
        return str_replace( array_keys( $variables ), $variables, $text );
    }
    
    public function parse_post_content( $text )
    {
        $text = wpautop( $text );
        //Add paragraph tags
        $text = preg_replace( "~(?:\\[/?)[^\\]]+/?\\]~s", '', $text );
        //Strip shortcodes
        $parse_html = new Html2Text( $text, array(
            'width' => 0,
        ) );
        $text = $parse_html->getText();
        $text = trim( $text );
        return $text;
    }

}
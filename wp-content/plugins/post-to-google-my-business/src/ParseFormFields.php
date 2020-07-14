<?php

namespace PGMB;

use  Exception ;
use  InvalidArgumentException ;
use  PGMB\API\APIInterface ;
use  PGMB\Google\LocalPost ;
use  PGMB\Google\MediaItem ;
use  PGMB\Placeholders\PostPermalink ;
use  PGMB\Placeholders\PostVariables ;
use  PGMB\Placeholders\SiteVariables ;
use  PGMB\Placeholders\UserVariables ;
use  PGMB\Placeholders\LocationVariables ;
use  PGMB\Placeholders\VariableInterface ;
use  PGMB\Vendor\Cron\CronExpression ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeImmutable ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeInterface ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTime ;
class ParseFormFields
{
    private  $form_fields ;
    public function __construct( $form_fields )
    {
        if ( !is_array( $form_fields ) ) {
            throw new InvalidArgumentException( 'ParseFormFields expects Form Fields array' );
        }
        $this->form_fields = $form_fields;
    }
    
    /**
     * Get DateTime object representing the when a post will be first published
     *
     * @return bool|WpDateTime|false DateTime when the post is first published, or false when the post isn't scheduled
     * @throws Exception Invalid DateTime
     */
    public function getPublishDateTime()
    {
        return false;
    }
    
    /**
     * Parse the form fields and return a LocalPost object
     *
     * @param APIInterface $api
     * @param $parent_post_id
     *
     * @param $location_name
     *
     * @return LocalPost
     * @throws Exception
     */
    public function getLocalPost( APIInterface $api, $parent_post_id, $location_name )
    {
        if ( !is_numeric( $parent_post_id ) ) {
            throw new InvalidArgumentException( 'Parent Post ID required for placeholder parsing' );
        }
        $location = $api->get_location( $location_name, false );
        $placeholder_variables = $this->generate_placeholder_variables( $parent_post_id, $location );
        $summary = stripslashes( $this->form_fields['mbp_post_text'] );
        if ( mbp_fs()->is_plan_or_trial( 'business' ) ) {
            $summary = \MBP_Spintax::Parse( $summary );
        }
        $summary = $this->parse_placeholder_variables( $placeholder_variables, $summary );
        $summary = mb_strimwidth(
            $summary,
            0,
            1500,
            "..."
        );
        $topicType = $this->form_fields['mbp_topic_type'];
        $localPost = new LocalPost( $location->languageCode, $summary, $topicType );
        //Set alert type
        if ( $topicType === 'ALERT' ) {
            $localPost->setAlertType( $this->form_fields['mbp_alert_type'] );
        }
        //Add image/video
        $mediaItem = $this->get_media_item( $parent_post_id );
        if ( !empty($mediaItem) && $topicType !== 'ALERT' ) {
            $localPost->addMediaItem( $mediaItem );
        }
        // mbp_content_image mbp_featured_image
        //Add button
        
        if ( isset( $this->form_fields['mbp_button'] ) && $this->form_fields['mbp_button'] ) {
            $buttonURL = $this->parse_placeholder_variables( $placeholder_variables, $this->form_fields['mbp_button_url'] );
            $callToAction = new \PGMB\Google\CallToAction( $this->form_fields['mbp_button_type'], $buttonURL );
            $localPost->addCallToAction( $callToAction );
        }
        
        //Add offer
        
        if ( $topicType == 'OFFER' ) {
            $localPostOffer = new \PGMB\Google\LocalPostOffer( $this->form_fields['mbp_offer_coupon'], $this->form_fields['mbp_offer_redeemlink'], $this->form_fields['mbp_offer_terms'] );
            $localPost->addLocalPostOffer( $localPostOffer );
        }
        
        //Add Event (used by Offer too)
        
        if ( $topicType == 'OFFER' || $topicType == 'EVENT' ) {
            $eventTitle = ( $topicType == 'OFFER' ? $this->form_fields['mbp_offer_title'] : $this->form_fields['mbp_event_title'] );
            //get the appropriate event title
            $startdate = new \DateTime( $this->form_fields['mbp_event_start_date'], WpDateTimeZone::getWpTimezone() );
            $enddate = new \DateTime( $this->form_fields['mbp_event_end_date'], WpDateTimeZone::getWpTimezone() );
            $startDate = new \PGMB\Google\Date( $startdate->format( 'Y' ), $startdate->format( 'm' ), $startdate->format( 'd' ) );
            $startTime = new \PGMB\Google\TimeOfDay( $startdate->format( 'H' ), $startdate->format( 'i' ) );
            $endDate = new \PGMB\Google\Date( $enddate->format( 'Y' ), $enddate->format( 'm' ), $enddate->format( 'd' ) );
            $endTime = new \PGMB\Google\TimeOfDay( $enddate->format( 'H' ), $enddate->format( 'i' ) );
            $timeInterval = new \PGMB\Google\TimeInterval(
                $startDate,
                $startTime,
                $endDate,
                $endTime
            );
            if ( isset( $this->form_fields['mbp_event_all_day'] ) && $this->form_fields['mbp_event_all_day'] ) {
                $timeInterval->setAllDay( true );
            }
            $localPostEvent = new \PGMB\Google\LocalPostEvent( $eventTitle, $timeInterval );
            $localPost->addLocalPostEvent( $localPostEvent );
        }
        
        return $localPost;
    }
    
    public function get_media_items( $parent_post_id )
    {
        $mediaItems = [];
        if ( empty($this->form_fields['mbp_post_attachment']) || !is_array( $this->form_fields['mbp_post_attachment'] ) ) {
            return false;
        }
        foreach ( $this->form_fields['mbp_post_attachment'] as $type => $items ) {
            foreach ( $items as $item ) {
                $mediaItems[] = new MediaItem( $type, $item );
            }
        }
        return $mediaItems;
    }
    
    public function get_media_item( $parent_post_id )
    {
        
        if ( !empty($this->form_fields['mbp_post_attachment']) ) {
            $image_id = attachment_url_to_postid( $this->form_fields['mbp_post_attachment'] );
            if ( $image_id && wp_attachment_is_image( $image_id ) ) {
                $this->validate_image_size( $image_id );
            }
            return new \PGMB\Google\MediaItem( $this->form_fields['mbp_attachment_type'], $this->form_fields['mbp_post_attachment'] );
        } elseif ( isset( $this->form_fields['mbp_content_image'] ) && $this->form_fields['mbp_content_image'] && ($image_url = $this->get_content_image( $parent_post_id )) ) {
            return new \PGMB\Google\MediaItem( 'PHOTO', $image_url );
        } elseif ( isset( $this->form_fields['mbp_featured_image'] ) && $this->form_fields['mbp_featured_image'] && ($image_url = get_the_post_thumbnail_url( $parent_post_id, 'large' )) ) {
            $image_id = get_post_thumbnail_id( $parent_post_id );
            $this->validate_image_size( $image_id );
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
        $this->validate_image_size( $image->ID );
        return reset( $image_details );
        //Return the first item in the array (which is the url)
    }
    
    public function validate_image_size( $image_id )
    {
        $image_details = wp_get_attachment_image_src( $image_id, 'large' );
        if ( $image_details[1] < 250 || $image_details[2] < 250 ) {
            throw new InvalidArgumentException( sprintf( __( 'Post image must be at least 250x250px. Current image is: %dx%dpx', 'post-to-google-my-business' ), $image_details[1], $image_details[2] ) );
        }
        $image_file_size = filesize( get_attached_file( $image_id ) );
        
        if ( $image_file_size < 10240 ) {
            throw new InvalidArgumentException( __( 'Post image too small, must be at least 10KB', 'post-to-google-my-business' ) );
        } elseif ( $image_file_size > 5242880 ) {
            throw new InvalidArgumentException( __( 'Post image too big, must be 5MB at most', 'post-to-google-my-business' ) );
        }
    
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
        if ( !isset( $this->form_fields['mbp_selected_location'] ) || empty($this->form_fields['mbp_selected_location']) ) {
            return [ $default_location ];
        }
        
        if ( !is_array( $this->form_fields['mbp_selected_location'] ) ) {
            return [ $this->form_fields['mbp_selected_location'] ];
        } elseif ( is_array( $this->form_fields['mbp_selected_location'] ) ) {
            return $this->form_fields['mbp_selected_location'];
        }
        
        throw new \UnexpectedValueException( __( "Could not parse post locations", 'post-to-google-my-business' ) );
    }
    
    public function generate_placeholder_variables( $parent_post_id, $location )
    {
        $decorators = [
            'post_permalink'     => new PostPermalink( $parent_post_id ),
            'post_variables'     => new PostVariables( $parent_post_id ),
            'user_variables'     => new UserVariables( $parent_post_id ),
            'site_variables'     => new SiteVariables(),
            'location_variables' => new LocationVariables( $location ),
        ];
        $decorators = apply_filters(
            'mbp_placeholder_decorators',
            $decorators,
            $parent_post_id,
            $location
        );
        $variables = [];
        foreach ( $decorators as $decorator ) {
            if ( $decorator instanceof VariableInterface ) {
                $variables = array_merge( $variables, $decorator->variables() );
            }
        }
        $variables = apply_filters( 'mbp_placeholder_variables', $variables, $parent_post_id );
        return $variables;
    }
    
    public function parse_placeholder_variables( $variables, $text )
    {
        return str_replace( array_keys( $variables ), $variables, $text );
    }

}
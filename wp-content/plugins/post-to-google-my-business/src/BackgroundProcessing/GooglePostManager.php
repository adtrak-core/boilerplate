<?php

namespace PGMB\BackgroundProcessing;

use  PGMB\API\APIInterface ;
use  PGMB\Google\LocalPostEditMask ;
use  PGMB\ParseFormFields ;
use  PGMB\PostTypes\SubPost ;
use  WP_Error ;
class GooglePostManager
{
    protected  $defaultLocation ;
    protected  $batch_processor ;
    protected  $api ;
    protected function __construct( BackgroundProcess $batch_processor, APIInterface $api, $defaultLocation )
    {
        $this->defaultLocation = $defaultLocation;
        $this->batch_processor = $batch_processor;
        $this->api = $api;
    }
    
    public static function init( BackgroundProcess $batch_processor, APIInterface $api, $defaultLocation )
    {
        $instance = new self( $batch_processor, $api, $defaultLocation );
        $post_type = SubPost::POST_TYPE;
        add_action(
            "save_post_{$post_type}",
            array( $instance, 'on_create_or_update_google_posts' ),
            10,
            3
        );
        add_action(
            'mbp_create_google_post',
            [ $instance, 'create_google_post' ],
            10,
            2
        );
        add_action(
            'mbp_scheduled_google_post',
            [ $instance, 'queue_gmb_posts' ],
            10,
            2
        );
        add_action(
            'before_delete_post',
            array( $instance, 'before_delete_subpost' ),
            10,
            1
        );
        add_action(
            'mbp_delete_gmb_post',
            [ $api, 'delete_post' ],
            10,
            1
        );
        return $instance;
    }
    
    public function on_create_or_update_google_posts( $post_id, $post, $update )
    {
        if ( $post->post_status != 'publish' ) {
            return;
        }
        $this->queue_gmb_posts( $post_id );
    }
    
    public function before_delete_subpost( $post_id )
    {
        if ( get_post_type( $post_id ) !== SubPost::POST_TYPE ) {
            return;
        }
        wp_clear_scheduled_hook( 'mbp_scheduled_google_post', [ $post_id ] );
        $created_posts = get_post_meta( $post_id, 'mbp_posts', true );
        if ( !is_array( $created_posts ) ) {
            return;
        }
        foreach ( $created_posts as $created_post ) {
            $item = [
                'action' => 'mbp_delete_gmb_post',
                'args'   => [
                'name' => $created_post['name'],
            ],
            ];
            $this->batch_processor->push_to_queue( $item );
        }
        $this->batch_processor->save()->dispatch();
    }
    
    public function queue_gmb_posts( $post_id )
    {
        $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
        $data = new ParseFormFields( $form_fields );
        $postPublishDate = get_post_meta( $post_id, '_mbp_post_publish_date', true );
        if ( !$postPublishDate ) {
            update_post_meta( $post_id, '_mbp_post_publish_date', time() );
        }
        //Todo: Skip queue if its just 1 location?
        foreach ( $data->getLocations( $this->defaultLocation ) as $location ) {
            $item = [
                'action' => 'mbp_create_google_post',
                'args'   => [
                'post_id'  => $post_id,
                'location' => $location,
            ],
            ];
            //Todo: create interface and classes for batch items
            $this->batch_processor->push_to_queue( $item );
        }
        $this->batch_processor->save()->dispatch();
        $key = $this->batch_processor->get_batch_key();
        update_post_meta( $post_id, '_mbp_post_batch_key', $key );
    }
    
    public function create_google_post( $post_id, $location )
    {
        $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
        $parent_post_id = wp_get_post_parent_id( $post_id );
        $created_posts = get_post_meta( $post_id, 'mbp_posts', true );
        $post_errors = get_post_meta( $post_id, 'mbp_errors', true );
        if ( !$created_posts ) {
            $created_posts = [];
        }
        if ( !$post_errors ) {
            $post_errors = [];
        }
        $is_autopost = get_post_meta( $post_id, '_mbp_is_autopost', true );
        try {
            $data = new ParseFormFields( $form_fields );
            $localPost = $data->getLocalPost( $this->api, $parent_post_id, $location );
            
            if ( array_key_exists( $location, $created_posts ) ) {
                $oldPost = $this->api->get_post( $created_posts[$location]['name'] );
                $mask = new LocalPostEditMask( $oldPost, $localPost );
                $localPost = apply_filters( 'mbp_update_post', $localPost, $post_id );
                $publishedLocalPost = $this->api->update_post( $created_posts[$location]['name'], $localPost->getArray(), $mask->getMask() );
            } else {
                $localPost = apply_filters(
                    'mbp_create_post',
                    $localPost,
                    $post_id,
                    $is_autopost
                );
                //Backward compatibility
                $filtered_post_args = ( $is_autopost ? apply_filters( 'mbp_autopost_post_args', $localPost->getArray(), $location ) : $localPost->getArray() );
                $publishedLocalPost = $this->api->create_post( $location, $filtered_post_args );
            }
            
            $created_posts[$location] = $publishedLocalPost->getArray();
            unset( $post_errors[$location] );
        } catch ( \Exception $e ) {
            $post_errors[$location] = new WP_Error( 'post_creation_error', sprintf( __( 'Failed to create/update post: %s', 'post-to-google-my-business' ), $e->getMessage() ) );
        }
        update_post_meta( $post_id, 'mbp_posts', $created_posts );
        update_post_meta( $post_id, 'mbp_errors', $post_errors );
        sleep( 5 );
    }

}
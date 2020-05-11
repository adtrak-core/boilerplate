<?php

namespace PGMB;

class FormFields
{
    public static  $fields = array(
        'mbp_attachment_type'   => 'PHOTO',
        'mbp_topic_type'        => 'STANDARD',
        'mbp_alert_type'        => 'COVID_19',
        'mbp_post_attachment'   => '',
        'mbp_post_text'         => 'New post: %post_title% - %post_content%',
        'mbp_event_all_day'     => false,
        'mbp_event_title'       => '',
        'mbp_event_start_date'  => '',
        'mbp_event_end_date'    => '',
        'mbp_offer_title'       => '',
        'mbp_offer_coupon'      => '',
        'mbp_offer_redeemlink'  => '',
        'mbp_offer_terms'       => '',
        'mbp_button'            => false,
        'mbp_button_type'       => 'LEARN_MORE',
        'mbp_button_url'        => '%post_permalink%',
        'mbp_schedule'          => false,
        'mbp_scheduled_date'    => '',
        'mbp_cron_schedule'     => '0 12 * * 1',
        'mbp_repost'            => false,
        'mbp_repost_stop_after' => 'executions',
        'mbp_repost_stop_date'  => '',
        'mbp_reposts'           => 1,
        'mbp_selected_location' => array(),
        'mbp_content_image'     => false,
        'mbp_featured_image'    => true,
    ) ;
    public static function default_autopost_fields()
    {
        return self::$fields;
    }
    
    public static function default_post_fields()
    {
        //placeholder for future development
        $fields = self::$fields;
        $fields['mbp_post_text'] = '';
        return $fields;
    }

}
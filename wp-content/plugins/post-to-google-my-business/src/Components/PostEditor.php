<?php

namespace PGMB\Components;

use  PGMB\FormFields ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTime ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone ;
class PostEditor
{
    private  $ajax ;
    public  $fields ;
    public  $fieldName ;
    public function __construct( $isAjax = false, $values = array(), $fieldName = 'mbp_form_fields' )
    {
        $this->ajax = $isAjax;
        $this->fieldName = $fieldName;
        $this->fields = array_merge( FormFields::default_post_fields(), $values );
    }
    
    public function generate()
    {
        ob_start();
        require_once dirname( __FILE__ ) . '/../../templates/PostEditor.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
    public static function draw( $isAjax = false, $values = false, $fieldName = 'mbp_form_fields' )
    {
        $instance = new static( $isAjax, $values, $fieldName );
        echo  $instance->generate() ;
    }
    
    public function is_ajax_enabled()
    {
        return $this->ajax;
    }
    
    public function register_ajax_callbacks( $prefix )
    {
        add_action( "wp_ajax_{$prefix}_check_date", [ $this, 'ajax_validate_time' ] );
    }
    
    public function ajax_validate_time()
    {
        $timestring = sanitize_text_field( $_POST['timestring'] );
        try {
            //$datetime = new \DateTime($timestring, WpDateTimeZone::getWpTimezone());
            $datetime = new WpDateTime( $timestring, WpDateTimeZone::getWpTimezone() );
        } catch ( \Exception $e ) {
            wp_send_json_error();
        }
        wp_send_json_success( $datetime->formatDate() . " " . $datetime->formatTime() . __( ', Timezone: ', 'post-to-google-my-business' ) . WpDateTimeZone::getWpTimezone()->getName() );
    }

}
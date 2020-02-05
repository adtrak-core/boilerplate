<?php

namespace PGMB;

class Epitrove
{
    private static  $instance ;
    private  $license = "free" ;
    public function setLicense( $license )
    {
        if ( $license == "free" || $license == "pro" ) {
            $this->license = $license;
        }
    }
    
    public static function instance()
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function checkLicense()
    {
        if ( $this->license == "free" ) {
            return false;
        }
        return true;
    }
    
    public function add_filter( $filter, $function )
    {
        return true;
    }
    
    public function add_action( $action, $function )
    {
        return true;
    }
    
    public function can_use_premium_code()
    {
        return $this->checkLicense();
    }
    
    public function get_upgrade_url()
    {
        return "https://epitrove.com/?post_type=product&p=2443";
    }
    
    public function is_plan_or_trial()
    {
        return $this->checkLicense();
    }
    
    public function is_not_paying()
    {
        return !$this->checkLicense();
    }

}
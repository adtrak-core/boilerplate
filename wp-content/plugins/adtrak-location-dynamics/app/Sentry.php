<?php
namespace Adtrak\LocationDynamics;

/**
 * Class Error
 * @package Billy\Framework
 */
class Sentry {

    /**
     * @var \Raven_ErrorHandler
     */
    protected static $sentry;

    /**
     * The booted state.
     * @var boolean
     */
    protected static $booted = false;

    /**
     * The base path
     * @var string
     */
    protected static $base;

    /**
     * Boots the Helper.
     */
    public static function boot()
    {
        self::$sentry = new \Raven_Client(Helper::get('sentryUrl'));
        self::$booted = true;
    }

    /**
     * @param $ex
     * @param array $params
     */
    public static function captureException($ex, $params = array())
    {
        if (!self::$booted) {
            self::boot();
        }

        // Provide some additional data with an exception
        self::$sentry->captureException($ex, $params);
    }


    public static function captureMessage($msg, $type = array(), $params = array())
    {
        if (!self::$booted) {
            self::boot();
        }

        // Provide some additional data with an exception
        self::$sentry->captureMessage($msg, $type, $params);
    }
}
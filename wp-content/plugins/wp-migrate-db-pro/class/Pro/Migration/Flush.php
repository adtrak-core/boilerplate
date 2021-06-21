<?php
/**
 * Backwards compatibility class.
 *
 * TODO: Remove after 2.0 beta, but test upgrade routines!
 */

namespace DeliciousBrains\WPMDB\Pro\Migration;

use DeliciousBrains\WPMDB\Common\Migration\Flush as Common_Flush;
use DeliciousBrains\WPMDB\Common\Http\Helper;
use DeliciousBrains\WPMDB\Common\Http\Http;
use DeliciousBrains\WPMDB\Common\Http\RemotePost;
use DeliciousBrains\WPMDB\Common\Util\Util;

class Flush extends Common_Flush
{
    /**
     * @var Helper
     */
    private $http_helper;
    /**
     * @var Util
     */
    private $util;
    /**
     * @var RemotePost
     */
    private $remote_post;
    /**
     * @var Http
     */
    private $http;

    public function __construct(
        Helper $helper,
        Util $util,
        RemotePost $remote_post,
        Http $http
    ) {
        parent::__construct($helper, $util, $remote_post, $http);
    }
}

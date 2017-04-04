<?php

namespace Billy\Framework\Facades;

use Illuminate\Support\Facades\Facade;
use Billy\Framework\Database;

/**
 * @see \Illuminate\Database\DatabaseManager
 * @see \Illuminate\Database\Connection
 */
class DB extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Database::instance();
    }
}
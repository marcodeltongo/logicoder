<?php
/**
 * Logicoder Web Application Framework - Abstract cache container
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Abstract cache container class.
 *
 * @package     Logicoder
 * @subpackage  Cache
 * @link        http://www.logicoder.com/documentation/cache.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_Cache_Abstract
{
    /**
     * Default TTL.
     */
    protected $iTTL;

    /**
     * Constructor.
     *
     * @param   integer $iTTL       Default TTL for cached data.
     */
    public function __construct ( $iTTL = 0 )
    {
        /*
            Save default TTL.
        */
        $this->iTTL = $iTTL;
    }

    /**
     * Cache a variable in the data store (only if it's not stored).
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     * @param   integer $iTTL       The Time To Live
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    abstract public function add ( $sKey, $mValue, $iTTL = null );

    /**
     * Cache a variable in the data store.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     * @param   integer $iTTL       The Time To Live
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    abstract public function set ( $sKey, $mValue, $iTTL = null );

    /**
     * Fetch a stored variable from the cache.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The stored variable on successor NULL on failure
     */
    abstract public function get ( $sKey );

    /**
     * Removes a stored variable from the cache.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    abstract public function delete ( $sKey );

    /**
     * Flush all existing items in the datastore.
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    abstract public function clean ( /* void */ );
}
// END Logicoder_Cache_Abstract class

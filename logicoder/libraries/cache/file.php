<?php
/**
 * Logicoder Web Application Framework - Cache to files container
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Cache to files container class.
 *
 * @package     Logicoder
 * @subpackage  Cache
 * @link        http://www.logicoder.com/documentation/cache.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Cache_File extends Logicoder_Cache_Abstract
{
    /**
     * Constructor.
     *
     * @param   array   $aOptions   Configuration options
     */
    public function __construct ( array $aOptions = null )
    {
        if (!is_null($aOptions) and is_assoc($aOptions))
        {
            /*
                Save options.
            */
        }
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
    public function add ( $sKey, $mValue, $iTTL = 0 )
    {
    }

    /**
     * Cache a variable in the data store.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     * @param   integer $iTTL       The Time To Live
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function set ( $sKey, $mValue, $iTTL = 0 )
    {
    }

    /**
     * Fetch a stored variable from the cache.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The stored variable on successor NULL on failure
     */
    public function get ( $sKey )
    {
    }

    /**
     * Removes a stored variable from the cache.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function delete ( $sKey )
    {
    }

    /**
     * Flush all existing items in the datastore.
     */
    public function clean ( /* void */ )
    {
    }
}
// END Logicoder_Cache_File class

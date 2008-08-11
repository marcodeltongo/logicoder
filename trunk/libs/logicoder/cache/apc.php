<?php
/**
 * Logicoder Web Application Framework - Cache to APC container
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Cache to APC container class.
 *
 * @package     Logicoder
 * @subpackage  Cache
 * @link        http://www.logicoder.com/documentation/cache.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Cache_APC extends Logicoder_Cache_Abstract
{
    /**
     * Cache a variable in the data store (only if it's not stored).
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     * @param   integer $iTTL       The Time To Live
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function add ( $sKey, $mValue, $iTTL = null )
    {
        return apc_add($sKey, $mValue, (isnull($iTTL) ? $this->iTTL : $iTTL));
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
    public function set ( $sKey, $mValue, $iTTL = null )
    {
        return apc_store($sKey, $mValue, (isnull($iTTL) ? $this->iTTL : $iTTL));
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
        $mVal = apc_fetch($sKey);
        return ($mVal !== false) ? $mVal : null;
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
        return apc_delete($sKey);
    }

    /**
     * Flush all existing items in the datastore.
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function clean ( /* void */ )
    {
        return apc_clear_cache();
    }
}
// END Logicoder_Cache_APC class

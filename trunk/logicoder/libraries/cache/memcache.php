<?php
/**
 * Logicoder Web Application Framework - Cache to memcache container
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Cache to memcache container class.
 *
 * @package     Logicoder
 * @subpackage  Cache
 * @link        http://www.logicoder.com/documentation/cache.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Cache_Memcache extends Logicoder_Cache_Abstract
{
    /**
     * Memcache connector.
     */
    protected $oMemcache;

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
        $this->oMemcache = new MemCache();
    }

    /**
     * Connects to a server running the memcache daemon.
     *
     * @param   string  $sHost
     * @param   integer $iPort
     *
     * @return  boolean TRUE on success or FALSE on failure
     */
    public function connect ( $sHost = 'localhost', $iPort = 11211 )
    {
        return $this->oMemcache->addServer($sHost, $iPort);
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
        return $this->oMemcache->add($sKey, $mValue, false, $iTTL);
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
        return $this->oMemcache->set($sKey, $mValue, false, $iTTL);
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
        $mVal = $this->oMemcache->get($sKey);
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
        return $this->oMemcache->delete($sKey);
    }

    /**
     * Flush all existing items in the datastore.
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function clean ( /* void */ )
    {
        return $this->oMemcache->flush();
    }
}
// END Logicoder_Cache_Memcache class

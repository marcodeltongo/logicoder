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
     * Path to the cache directory.
     */
    protected $sPath;

    /**
     * Constructor.
     *
     * @param   integer $iTTL       Default TTL for cached data.
     */
    public function __construct ( $iTTL = 0, $sPath = null )
    {
        parent::__construct($iTTL);
        /*
            Save options.
        */
        $this->sPath = is_null($sPath) ? ini_get('session.save_path') . '/cache/' : $sPath;
    }

    /**
     * Build a filename for the key.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  string  Complete path and filename for key
     */
    protected function filename ( $sKey )
    {
        return $this->sPath . crc32($sKey) . ".cache";
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
    public function add ( $sKey, $mValue, $iTTL = null )
    {
        if (!file_exists($this->filename($sKey)))
        {
            return $this->set($sKey, $mValue, $iTTL);
        }
        return false;
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
        $iTTL = isnull($iTTL) ? $this->iTTL : $iTTL;
        $sTTL = (time() + $iTTL) . "\n";
        $bOK = file_put_contents($this->filename($sKey), $sTTL . serialize($mValue), LOCK_EX);
        return ($bOK !== false);
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
        $sFile = $this->filename($sKey);
        if (file_exists($sFile))
        {
            /*
                Open and lock cache.
            */
            if (($cache = @$fopen($sFile, 'r')) === false)
            {
                return null;
            }
            flock($cache, LOCK_SH);
            /*
                Get TTL and check if stale.
            */
            $iTTL = (integer)fgets($cache);
            if (time() > $iTTL)
            {
                fclose($cache);
                unlink($sFile);
                return null;
            }
            /*
                Get data.
            */
            $data = '';
            while (feof($cache))
            {
                $data .= fread($cache, 4096);
            }
            fclose($cache);
            /*
                Unserialize data, check and return it.
            */
            if (($data = @unserialize($data)) === false)
            {
                unlink($sFile);
                return null;
            }
            return $data;
        }
        return null;
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
        $sFile = $this->filename($sKey);
        if (file_exists($sFile))
        {
            return unlink($sFile);
        }
        return false;
    }

    /**
     * Flush all existing items in the datastore.
     */
    public function clean ( /* void */ )
    {
        $sOldDir = getcwd();
        if (!chdir($this->sPath))
        {
            return false;
        }
        foreach(glob('*.cache') as $sFile)
        {
            unlink($sFile);
        }
        chdir($sOldDir);
        return true;
    }
}
// END Logicoder_Cache_File class

<?php
/**
 * Logicoder Web Application Framework - HTTP Session library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * HTTP Session class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/sessions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_HTTP_Session implements Logicoder_iSingleton, ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Session cookie params.
     */
    protected $aCookieParams;

    /**
     * Flash values session key.
     */
    const FLASH = '__FLASHDATA__';

    // -------------------------------------------------------------------------
    //  Singleton interface implementation.
    // -------------------------------------------------------------------------

    /**
     * Private Constructor, don't call it.
     */
    private function __construct ( /* void */ ) { /* void */ }

    /**
     *  Returns the singleton instance of the class.
     *
     *  @return object  The singleton instance
     */
    public static function instance ( /* void */ )
    {
        static $oInstance = null;
        if (is_null($oInstance))
        {
            $oInstance = new Logicoder_HTTP_Session();
            $oInstance->start();
        }
        return $oInstance;
    }

    // -------------------------------------------------------------------------
    //  Session implementation.
    // -------------------------------------------------------------------------

    /**
     * Start a session.
     *
     * @param   string  $sName          Session name
     * @param   array   $aCookieParams  Cookie parameters
     */
    public function start ( $sName = 'logicoder', array $aCookieParams = null )
    {
        /*
            Setup name, start and get session input filter.
        */
        if (session_id() === '' and PHP_SAPI != 'cli') {
            if (!is_null($aCookieParams))
            {
                call_user_func_array('session_set_cookie_params', $aCookieParams);
            }
            session_name($sName);
            session_start();
        }
        $this->aCookieParams = is_null($aCookieParams) ? session_get_cookie_params() : $aCookieParams;
        /*
            Check if we need to regenerate session.
        */
        if ($_SESSION['__initialized__'] !== true)
        {
            session_regenerate_id(true);
            $_SESSION['__initialized__'] = true;
        }
        /*
            Check if we need to create flash values array.
        */
        if (!isset($_SESSION[self::FLASH]))
        {
            $_SESSION[self::FLASH] = array();
        }
    }

    /**
     * Regenerate session ID.
     */
    public function regenerate ( /* void */ )
    {
        session_regenerate_id(true);
    }

    /**
     * Is a variable stored ?
     *
     * @param   string  $sKey       The name/key string
     * @param   boolean $bFlash     Whether this is a flash item or not
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function has ( $sKey, $bFlash = false )
    {
        if ($bFlash)
        {
            return (isset($_SESSION[self::FLASH][$sKey]));
        }
        else
        {
            return isset($_SESSION[$sKey]);
        }
    }

    /**
     * Add a variable to the session (only if it's not already stored).
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     * @param   boolean $bFlash     Whether this is a flash item or not
     *
     * @return  boolean TRUE on success or FALSE on failure.
     */
    public function add ( $sKey, $mValue, $bFlash = false )
    {
        if ($bFlash)
        {
            if (!isset($_SESSION[self::FLASH][$sKey]))
            {
                $_SESSION[self::FLASH][$sKey] = $mValue;
                return true;
            }
            return false;
        }
        else
        {
            if (!isset($_SESSION[$sKey]))
            {
                $_SESSION[$sKey] = $mValue;
                return true;
            }
            return false;
        }
    }

    /**
     * Add a variable to the session.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     * @param   boolean $bFlash     Whether this is a flash item or not
     */
    public function set ( $sKey, $mValue, $bFlash = false )
    {
        if ($bFlash)
        {
            $_SESSION[self::FLASH][$sKey] = $mValue;
        }
        else
        {
            $_SESSION[$sKey] = $mValue;
        }
    }

    /**
     * Fetch a stored variable from the session.
     *
     * NOTE: In case of a flash value it will be deleted upon get.
     *
     * @param   string  $sKey       The name/key string
     * @param   boolean $bFlash     Whether this is a flash item or not
     * @param   boolean $bKeep      Whether to keep flash item or not
     *
     * @return  mixed   The stored variable on successor NULL on failure
     */
    public function get ( $sKey, $bFlash = false, $bKeep = false )
    {
        if ($bFlash)
        {
            if (isset($_SESSION[self::FLASH][$sKey]))
            {
                $mValue = $_SESSION[self::FLASH][$sKey];
                if (!$bKeep)
                {
                    unset($_SESSION[self::FLASH][$sKey]);
                }
                return $mValue;
            }
            return null;
        }
        else
        {
            return (isset($_SESSION[$sKey])) ? $_SESSION[$sKey] : null;
        }
    }

    /**
     * Removes a stored variable from the session.
     *
     * @param   string  $sKey       The name/key string
     * @param   boolean $bFlash     Whether this is a flash item or not
     */
    public function delete ( $sKey, $bFlash = false )
    {
        if ($bFlash)
        {
            unset($_SESSION[self::FLASH][$sKey]);
        }
        else
        {
            unset($_SESSION[$sKey]);
        }
    }

    /**
     * Flush all existing items in the session.
     */
    public function clean ( /* void */ )
    {
        $_SESSION = array();
        $_SESSION[self::FLASH] = array();
    }

    // -------------------------------------------------------------------------
    //  Interfaces and magic implementations.
    // -------------------------------------------------------------------------

    /**
     * Overload magic property setter method.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value
     */
    protected function __set ( $sKey, $mValue )
    {
        return $this->set($sKey, $mValue, isset($_SESSION[self::FLASH][$sKey]));
    }

    /**
     * Overload magic property getter method.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The key value
     */
    protected function __get ( $sKey )
    {
        return $this->get($sKey, isset($_SESSION[self::FLASH][$sKey]));
    }

    /**
     * Overload magic property checker method.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    protected function __isset ( $sKey )
    {
        return isset($_SESSION[$sKey]) or isset($_SESSION[self::FLASH][$sKey]);
    }

    /**
     * Overload magic property unsetter method.
     *
     * @param   string  $sKey       The name/key string
     */
    protected function __unset ( $sKey )
    {
        if (isset($_SESSION[self::FLASH][$sKey]))
        {
            unset($_SESSION[self::FLASH][$sKey]);
        }
        else
        {
            unset($_SESSION[$sKey]);
        }
    }

    /**
     * Implements ArrayAccess element setter.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value
     */
    public function offsetSet ( $sKey, $mValue )
    {
        return $this->__set($sKey, $mValue);
    }

    /**
     * Implements ArrayAccess element getter.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The key value
     */
    public function offsetGet ( $sKey )
    {
        return $this->__get($sKey);
    }

    /**
     * Implements ArrayAccess element unsetter.
     *
     * @param   string  $sKey       The name/key string
     */
    public function offsetUnset ( $sKey )
    {
        $this->__unset($sKey);
    }

    /**
     * Implements ArrayAccess element checker.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    public function offsetExists ( $sKey )
    {
        return $this->__isset($sKey);
    }

    /**
     * Implements IteratorAggregate getIterator method.
     *
     * @return  object  This function will return an iterator
     */
    public function getIterator ( /* void */ )
    {
        /*
            Merge common and flash data.
        */
        $aData = $_SESSION;
        $aData = array_merge($aData, $aData[self::FLASH]);
        unset($aData[self::FLASH]);
        return new ArrayIterator($aData);
    }

    /**
     * Countable interface implementation.
     *
     * @return  integer Total number of values stored
     */
    public function count ( /* void */ )
    {
        /*
            Add flash data to count.
        */
        return count($_SESSION) + count($_SESSION[self::FLASH]) - 1;
    }
}
// END Logicoder_Session class

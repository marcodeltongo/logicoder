<?php
/**
 * Logicoder Web Application Framework - Registry and ObjectRegistry library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**#@+
 * Required dependency.
 */
require('interfaces.php');
require('overarray.php');
/**#@-*/

// -----------------------------------------------------------------------------

/**
 * An implementation of the registry pattern.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/registry.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Registry extends Logicoder_OverArray implements Logicoder_iRegistry
{
    /**
     * Save in the register the value $mValue as $sKey.
     *
     * @param   string  $sKey   The name/key string
     * @param   mixed   $mValue The value
     *
     * @return  mixed   The key value
     */
    public function register ( $sKey, $mValue )
    {
        return $this->__set($sKey, $mValue);
    }

    /**
     * Destroy $sKey value in the registry.
     *
     * @param   string  $sKey   The name/key string
     */
    public function unregister ( $sKey )
    {
        $this->__unset($sKey);
    }

    /**
     * Returns the value of $sKey is in the registry.
     *
     * @param   string  $sKey   The name/key string
     *
     * @return  mixed   The key value
     */
    public function get ( $sKey )
    {
        return $this->__get($sKey);
    }

    /**
     * Returns true if $sKey is in the registry.
     *
     * @param   string  $sKey   The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    public function has ( $sKey )
    {
        return $this->__isset($sKey);
    }
}
// END Logicoder_Registry class

// -----------------------------------------------------------------------------

/**
 * An implementation of the registry pattern for objects.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/registry.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_ObjectRegistry extends Logicoder_Registry
{
    /**
     * Save in the register the object $oObject as $sKey.
     *
     * @param   string  $sKey   The name/key string
     * @param   object  $mValue The object
     *
     * @return  mixed   The object
     */
    public function register ( $sKey, $oObject )
    {
        if (is_object($oObject))
        {
            return $this->__set($sKey, $oObject);
        }
        else
        {
            throw new Exception('Logicoder_ObjectRegistry accepts only objects.');
        }
    }
}
// END Logicoder_ObjectRegistry class

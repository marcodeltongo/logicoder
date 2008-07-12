<?php
/**
 * Logicoder Web Application Framework - Interfaces library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * The Singleton Pattern Interface
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/interfaces.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
interface Logicoder_iSingleton
{
    /**
     * Returns singleton instance of the class.
     */
    public static function instance ( /* void */ );
}
// END Logicoder_iSingleton interface

/**
 * The Registry Pattern Interface
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/interfaces.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
interface Logicoder_iRegistry
{
    /**
     * Save in the register the value $mValue as $sKey.
     */
    public function register ( $sKey, $mValue );
    /**
     * Destroy $sKey value in the registry.
     */
    public function unregister ( $sKey );
    /**
     * Returns the value of $sKey in the registry.
     */
    public function get ( $sKey );
    /**
     * Returns true if $sKey is in the registry.
     */
    public function has ( $sKey );
}
// END Logicoder_iRegistry interface

/**
 * The Factory Pattern Interface
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/interfaces.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
interface Logicoder_iFactory
{
    /**
     * Returns a new instance of the class.
     */
    public static function instance ( /* void */ );
}
// END Logicoder_iFactory interface

/**
 * The Abstract Factory Pattern Interface
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/interfaces.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
interface Logicoder_iAbstractFactory
{
    /**
     * Returns a new instance of the factory class.
     */
    public static function factory ( /* void */ );
}
// END Logicoder_iAbstractFactory interface

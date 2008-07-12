<?php
/**
 * Logicoder Web Application Framework - Dummy Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Dummy Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_Dummy
{
    /**
     * Does nothing and returns true.
     *
     * @return  boolean Always true
     */
    public function __call ( $sName, $aArguments )
    {
        return true;
    }
}
// END Logicoder_DummyLogger class

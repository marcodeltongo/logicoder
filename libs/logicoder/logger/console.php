<?php
/**
 * Logicoder Web Application Framework - Console Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Console Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_Console extends Logicoder_Logger_Abstract
{
    /**
     * Prints a message if the level is below the threshold.
     *
     * @param   number  $nLevel     Message level
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function log ( $nLevel, $sMessage = '' )
    {
        if (parent::log($nLevel) and $sMessage !== '')
        {
            echo "\n[LOG] " . str_replace('|','-',trim($sMessage)) . "\n";
            return true;
        }
        return false;
    }
}
// END Logicoder_ConsoleLogger class

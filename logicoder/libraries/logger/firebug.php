<?php
/**
 * Logicoder Web Application Framework - Firebug Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Firebug Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @see         http://cvs.php.net/viewvc.cgi/pear/Log/Log/firebug.php?view=markup
 */
class Logicoder_Logger_Firebug extends Logicoder_Logger_Abstract
{
    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD )
    {
        throw new Exception('THIS CLASS IS UNDER VERY HEAVY CONSTRUCTION !!!');
        parent::__construct($nThreshold);
    }

    /**
     * Logs a message if the level is below the threshold.
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
            $sMessage = addslashes($sMessage);
            switch ($nLevel)
            {
                case LOG_EMERG:
                case LOG_ALERT:
                case LOG_CRIT:
                case LOG_ERR:
                    echo "<script>console.error('$sMessage')</script>\n";
                break;

                case LOG_WARNING:
                    echo "<script>console.warn('$sMessage')</script>\n";
                break;

                case LOG_NOTICE:
                case LOG_INFO:
                case LOG_DEBUG:
                    echo "<script>console.info('$sMessage')</script>\n";
            }
            return true;
        }
        return false;
    }
}
// END Logicoder_FirebugLogger class

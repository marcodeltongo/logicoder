<?php
/**
 * Logicoder Web Application Framework - Abstract Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Abstract Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_Logger_Abstract
{
    /**
     * Limit for message logging.
     */
    protected $nThreshold;

    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD )
    {
        $this->nThreshold = ++$nThreshold;
        $this->debug('Logging subsystem started.');
    }

    /**
     * Logs a message if the level is below the threshold.
     *
     * @param   number  $nLevel     Message level
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message should be logged
     */
    public function log ( $nLevel, $sMessage = '' )
    {
        return ($nLevel < $this->nThreshold);
    }

    /**
     * Logs a debug message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function debug ( $sMessage )
    {
        $sMessage = '    DEBUG | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_DEBUG, $sMessage);
    }

    /**
     * Logs an informational message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function info ( $sMessage )
    {
        $sMessage = '     INFO | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_INFO, $sMessage);
    }

    /**
     * Logs a notice message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function notice ( $sMessage )
    {
        $sMessage = '   NOTICE | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_NOTICE, $sMessage);
    }

    /**
     * Logs a warning message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function warning ( $sMessage )
    {
        $sMessage = '  WARNING | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_WARNING, $sMessage);
    }

    /**
     * Logs an error message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function error ( $sMessage )
    {
        $sMessage = '    ERROR | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_ERR, $sMessage);
    }

    /**
     * Logs a critical message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function critical ( $sMessage )
    {
        $sMessage = ' CRITICAL | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_CRIT, $sMessage);
    }

    /**
     * Logs an alert message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function alert ( $sMessage )
    {
        $sMessage = '    ALERT | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_ALERT, $sMessage);
    }

    /**
     * Logs an emergency message.
     *
     * @param   string  $sMessage   The message
     *
     * @return  boolean Returns true if message has been logged
     */
    public function emergency ( $sMessage )
    {
        $sMessage = 'EMERGENCY | ' . date(LOG_DATE_FORMAT) . ' | ' . trim($sMessage);
        return $this->log(LOG_EMERG, $sMessage);
    }
}
// END Logicoder_AbstractLogger class

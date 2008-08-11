<?php
/**
 * Logicoder Web Application Framework - File Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * File Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_File extends Logicoder_Logger_Abstract
{
    /**
     * Destination file
     */
    protected $sFile;

    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     * @param   string  $sFile      Destination filename
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD, $sFile = LOG_FILE )
    {
        if (!is_writable($sFile) and !touch($sFile))
        {
            throw new Exception(__CLASS__ . " can't use $sFile as logfile.");
        }
        $this->sFile = realpath($sFile);
        parent::__construct($nThreshold);
    }

    /**
     * Write a line in log file if level is below the threshold.
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
            return error_log($sMessage . "\n", FILE_LOGGER, $this->sFile);
        }
        return false;
    }
}
// END Logicoder_FileLogger class

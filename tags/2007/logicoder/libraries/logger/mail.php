<?php
/**
 * Logicoder Web Application Framework - Mail Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Mail Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_Mail extends Logicoder_Logger_Abstract
{
    /**
     * Destination email address
     */
    protected $sMailTo;

    /**
     * Additional mail headers
     */
    protected $sHeaders;

    /**
     * Internal buffer
     */
    protected $sBuffer;

    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     * @param   string  $sMailTo    Destination email address
     * @param   string  $sHeaders   Additional mail headers
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD, $sMailTo = LOG_MAILTO, $sHeaders = LOG_HEADERS )
    {
        $this->sMailTo = $sMailTo;
        $this->sHeaders = $sHeaders;
        $this->sBuffer = '';
        parent::__construct($nThreshold);
    }

    /**
     * Save message in buffer if level is below the threshold.
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
            $this->sBuffer .= "\n$sMessage";
            return true;
        }
        return false;
    }

    /**
     * Sends an email if needed.
     */
    public function __destruct ( /* void */ )
    {
        if ($this->sBuffer !== '')
        {
            $this->sBuffer = "\nMAIL LOGGER REPORT:\n" . $this->sBuffer . "\n\nEND REPORT\n";
            mail($this->sMailTo, 'MAIL LOGGER REPORT', $this->sBuffer, $this->sHeaders);
        }
    }
}
// END Logicoder_MailLogger class

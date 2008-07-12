<?php
/**
 * Logicoder Web Application Framework - Comment Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Logger class which outputs HTML comments.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_Comment extends Logicoder_Logger_Abstract
{
    /**
     * Internal buffer
     */
    protected $sBuffer;

    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     * @param   boolean $bBuffered  Whether to write immediately or at end
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD, $bBuffered = LOG_BUFFERED )
    {
        $this->sBuffer = false;
        if ($bBuffered)
        {
            $this->sBuffer = "\n<!-- LOGGER MESSAGES:\n";
        }

        parent::__construct($nThreshold);
    }

    /**
     * Logs message as HTML comment if level is below the threshold.
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
            if ($this->sBuffer === false)
            {
                echo "\n<!-- LOGGER MESSAGE\n" . $sMessage . "\n-->\n";
            }
            else
            {
                $this->sBuffer .= "\n$sMessage";
            }
            return true;
        }
        return false;
    }

    /**
     * Prints buffer if needed.
     */
    public function __destruct ()
    {
        if ($this->sBuffer !== false)
        {
            echo $this->sBuffer . "\n\n-->\n";
        }
    }
}
// END Logicoder_CommentLogger class

<?php
/**
 * Logicoder Web Application Framework - Loggers library
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
require('constants.php');
/**#@-*/

// -----------------------------------------------------------------------------

/**
 * Logger factory class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger implements Logicoder_iFactory
{
    /**
     * Private Constructor, don't call it.
     */
    private function __construct ( /* void */ ) { /* void */ }

    /**
     * Factory method.
     *
     * @return  object  Returns a new instance
     */
    public static function instance ( /* void */ )
    {
        /*
            Return dummy logger if logging not required.
        */
        if (LOG_ACTIVE === false)
        {
            return new Logicoder_DummyLogger();
        }
        /*
            Prepare parameters.
        */
        $aParams = array_slice(func_get_args(), 1);
        $aParams = empty($aParams) ? null : $aParams;
        /*
            Select the required class.
        */
        switch (LOG_TYPE)
        {
            case MAIL_LOGGER:
                $sClass = 'Logicoder_MailLogger';
            break;
            case FILE_LOGGER:
                $sClass = 'Logicoder_FileLogger';
            break;
            case DATEFILE_LOGGER:
                $sClass = 'Logicoder_DateFileLogger';
            break;
            case COMMENT_LOGGER:
                $sClass = 'Logicoder_CommentLogger';
            break;
            case WINDOW_LOGGER:
                $sClass = 'Logicoder_WindowLogger';
            break;
            case CONSOLE_LOGGER:
                $sClass = 'Logicoder_ConsoleLogger';
            break;
            case DUMMY_LOGGER:
                $sClass = 'Logicoder_DummyLogger';
            break;
        }
        /*
            Return a new instance.
        */
        return (empty($aParams)) ? new $sClass() : new $sClass($aParams);
    }
}
// END Logicoder_Logger class

// -----------------------------------------------------------------------------

/**
 * Dummy Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DummyLogger
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

// -----------------------------------------------------------------------------

/**
 * Abstract Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_AbstractLogger
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

// -----------------------------------------------------------------------------

/**
 * Mail Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_MailLogger extends Logicoder_AbstractLogger
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

// -----------------------------------------------------------------------------

/**
 * File Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_FileLogger extends Logicoder_AbstractLogger
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

// -----------------------------------------------------------------------------

/**
 * File Logger class with date-based filenames.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DateFileLogger extends Logicoder_FileLogger
{
    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     * @param   string  $sPath      Path to destination dir
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD, $sPath = LOG_PATH )
    {
        parent::__construct(realpath($sPath) . '/' . date('Y_m_d') . '.log', $nThreshold);
    }
}
// END Logicoder_DateFileLogger class

// -----------------------------------------------------------------------------

/**
 * Logger class which outputs HTML comments.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_CommentLogger extends Logicoder_AbstractLogger
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

// -----------------------------------------------------------------------------

/**
 * Console Logger class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_ConsoleLogger extends Logicoder_AbstractLogger
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
class Logicoder_FirebugLogger extends Logicoder_AbstractLogger
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

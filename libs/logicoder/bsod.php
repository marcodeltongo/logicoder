<?php
/**
 * Logicoder Web Application Framework - BSOD library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
if (!defined('E_RECOVERABLE_ERROR'))
{
    define('E_RECOVERABLE_ERROR', 4096);
}

// -----------------------------------------------------------------------------


/**
 * Error and Exception Handler (aka Blue Screen of Death)
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/bsod.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_BSOD
{
    /**
     * True if error handler installed.
     */
    private $bErrorHandler;

    /**
     * True if exception handler installed.
     */
    private $bExceptionHandler;

    /**
     * PHP severity levels.
     */
    private $aLevels;

    /**
     * PHP error levels.
     */
    private $aErrors    = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_ALL, E_RECOVERABLE_ERROR);

    /**
     * PHP warning levels.
     */
    private $aWarnings  = array(E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING);

    /**
     * PHP notice levels.
     */
    private $aNotices   = array(E_NOTICE, E_USER_NOTICE, E_STRICT);

    /**#@+
     * Error and exception information.
     */
    private $title;
    private $level;
    private $type;
    private $code;
    private $uri;
    private $message;
    private $line;
    private $file;
    private $trace;
    /**#@-*/

    /**
     * Constructor, sets handlers if asked.
     *
     * @param   boolean $bErrorHandler      Set as error handler
     * @param   boolean $bExceptionHandler  Set as exception handler
     */
    public function __construct ( $bErrorHandler = true, $bExceptionHandler = true )
    {
        /*
            Set the error and exception handlers.
        */
        if ($bErrorHandler)
        {
            set_error_handler(array(&$this, 'handle_error'));
        }
        $this->bErrorHandler = $bErrorHandler;
        if ($bExceptionHandler)
        {
            set_exception_handler(array(&$this, 'handle_exception'));
        }
        $this->bExceptionHandler = $bExceptionHandler;
    }

    /**
     * Destructor, restores handlers if needed.
     */
    public function __destruct ()
    {
        /*
            Reset the error and exception handlers.
        */
        if ($this->bErrorHandler)
        {
            restore_error_handler();
        }
        if ($this->bExceptionHandler)
        {
            restore_exception_handler();
        }
    }

    /**
     * Handles errors.
     *
     * @param   number  $nSeverity  Error severity
     * @param   string  $sMessage   Error message
     * @param   string  $sFile      Source file where error occured
     * @param   number  $nLine      Line number where error occured
     */
    public function handle_error ( $nSeverity, $sMessage, $sFile, $nLine )
    {
        /*
            Should we manage this error ?
        */
        if (($nSeverity > 0) and !(error_reporting() & $nSeverity))
        {
            return;
        }
        /*
            Fill up with details of the error.
        */
        $this->level    = $nSeverity;
        $this->type     = 'Error';
        $this->uri      = htmlspecialchars($_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);
        $this->title    = $this->type . htmlspecialchars(" making " . $_SERVER['REQUEST_METHOD'] . " request to " . $_SERVER['REQUEST_URI']);
        /*
            Map local level to logger levels.
        */
        if (in_array($nSeverity, $this->aNotices))
        {
            $this->code = "[$nSeverity] NOTICE";
        }
        elseif (in_array($nSeverity, $this->aWarnings))
        {
            $this->code = "[$nSeverity] WARNING";
        }
        elseif (in_array($nSeverity, $this->aErrors))
        {
            $this->code = "[$nSeverity] ERROR";
        }
        /*
            Add error informations.
        */
        $this->message  = preg_replace("%\s\[<a href='function\.[\d\w-_]+'>function\.[\d\w-_]+</a>\]%", '', $sMessage);
        $this->line     = $nLine;
        $this->file     = $sFile;
        $this->trace    = array();
        /*
            Building exception-like backtrace
        */
        $backtrace = debug_backtrace();
        for($i = 1; $i < sizeof($backtrace); $i++) {
            $this->trace[$i - 1] = @$backtrace[$i];
        }
        /*
            Log error.
        */
        $this->log();
        /*
            Mail error.
        */
        $this->mail();
        /*
            Display error.
        */
        $this->display();
    }

    /**
     * Handles exceptions.
     *
     * @param   object  $oException Exception to handle
     */
    public function handle_exception ( $oException )
    {
        /*
            Fill up with details like an error.
        */
        $this->level    = E_ERROR;
        $this->type     = htmlspecialchars(get_class($oException));
        $this->uri      = htmlspecialchars($_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);
        $this->title    = $this->type . htmlspecialchars(' making ' . $_SERVER['REQUEST_METHOD'] . ' request to ' . $_SERVER['REQUEST_URI']);
        $this->code     = htmlspecialchars($oException->getCode());
        if ($this->code == 0)
        {
            $this->code = strtoupper(get_class($oException));
        }
        else
        {
            $this->code = "[{$this->code}] " . strtoupper(get_class($oException));
        }
        $this->message  = htmlspecialchars($oException->getMessage());
        $this->line     = htmlspecialchars($oException->getLine());
        $this->file     = htmlspecialchars($oException->getFile());
        $this->trace    = $oException->getTrace();
        /*
            Log exception.
        */
        $this->log();
        /*
            Mail exception.
        */
        $this->mail();
        /*
            Display exception.
        */
        $this->display();
    }

    /**
     * Saves error|exception in log.
     */
    private function log ( /* void */ )
    {
        if (!defined('LOG_TYPE') or LOG_TYPE == CONSOLE_LOGGER)
        {
            return;
        }
        if (class_exists('Logicoder'))
        {
            /*
                Get logger from core instance.
            */
            $logger = Logicoder::instance()->logger;
            /*
                Map local level to logger levels.
            */
            if (in_array($this->level, $this->aNotices))
            {
                return $logger->notice($this->message);
            }
            elseif (in_array($this->level, $this->aWarnings))
            {
                return $logger->warning($this->message);
            }
            elseif (in_array($this->level, $this->aErrors))
            {
                return $logger->error($this->message);
            }
            else
            {
                return $logger->debug($this->message);
            }
        }
        else
        {
            return error_log($this->message);
        }
    }

    /**
     * Sends error|exception via email.
     */
    private function mail ( /* void */ )
    {
        if (!defined('LOG_MAIL_THRESHOLD') or !class_exists('Logicoder_MailLogger'))
        {
            return;
        }
        /*
            Get a new mail logger from core instance.
        */
        $logger = new Logicoder_MailLogger(LOG_MAIL_THRESHOLD);
        /*
            Map local level to logger levels.
        */
        if (in_array($this->level, $this->aNotices))
        {
            return $logger->notice($this->message);
        }
        elseif (in_array($this->level, $this->aWarnings))
        {
            return $logger->warning($this->message);
        }
        elseif (in_array($this->level, $this->aErrors))
        {
            return $logger->error($this->message);
        }
        else
        {
            return $logger->debug($this->message);
        }
    }

    /**
     * Displays an HTML Blue Screen Of Death !
     */
    private function display_html ( /* void */ )
    {
    }

    /**
     * Displays a textual Blue Screen Of Death !
     */
    private function display_text ( /* void */ )
    {
    }

    /**
     * Displays the Blue Screen Of Death !
     */
    private function display ( /* void */ )
    {
        /*
            Get rid of previous output.
        */
        while (ob_get_level())
        {
            ob_end_clean();
        }
        /*
            HTML or TEXT ?
        */
        if (PHP_SAPI == 'cli')
        {
            $this->display_text();
        }
        else
        {
            $this->display_html();
        }
        /*
            Bye bye.
        */
        exit($this->message);
    }
}
// END Logicoder_BSOD class

<?php
/**
 * Logicoder Web Application Framework - HTTP Request library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * The HTTP Request class.
 *
 * @package     Logicoder
 * @subpackage  HTTP
 * @link        http://www.logicoder.com/documentation/request.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Request
{
    /**#@+
     * HTTP DATA OBJECTS
     */
    public $GET;
    public $POST;
    public $META;
    public $FILES;
    public $COOKIE;
    public $SESSION;
    /**#@-*/

    /**
     * Constructor.
     */
    public function __construct ( /* void */ )
    {
        /*
            Load Logicoder_InputFilter dependency.
        */
        $sClass = 'Logicoder_InputFilter';
        if (class_exists('Logicoder'))
        {
            $sClass = Logicoder::instance()->load->library('InputFilter');
        }
        else
        {
            require('inputfilter.php');
        }
        /*
            Pre-process everything ?
        */
        $bPreprocess = (REQUEST_XSS_FILTERING and !REQUEST_LAZY_FILTERING);
        /*
            Setup input filters.
        */
        $this->GET     = new $sClass('_GET',        $bPreprocess);
        $this->POST    = new $sClass('_POST',       $bPreprocess);
        $this->META    = new $sClass('_SERVER',     $bPreprocess);
        $this->FILES   = new $sClass('_FILES',      $bPreprocess);
        $this->COOKIE  = new $sClass('_COOKIE',     $bPreprocess);
        $this->SESSION = new $sClass('_SESSION',    $bPreprocess);
    }

    /**
     * Returns GET data.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   Default return value
     *
     * @return  mixed   HTTP GET value, default value or null
     */
    public function GET ( $sKey, $mDefault = null )
    {
        return (isset($this->GET[$sKey])) ? $this->GET[$sKey] : $mDefault;
    }

    /**
     * Returns POST data.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   Default return value
     *
     * @return  mixed   HTTP POST value, default value or null
     */
    public function POST ( $sKey, $mDefault = null )
    {
        return (isset($this->POST[$sKey])) ? $this->POST[$sKey] : $mDefault;
    }

    /**
     * Returns META and HEADER data.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   Default return value
     *
     * @return  mixed   HTTP META or HEADER value, default value or null
     */
    public function META ( $sKey, $mDefault = null )
    {
        return (isset($this->META[$sKey])) ? $this->META[$sKey] : $mDefault;
    }

    /**
     * Returns FILES data.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   Default return value
     *
     * @return  mixed   HTTP FILES value, default value or null
     */
    public function FILES ( $sKey, $mDefault = null )
    {
        return (isset($this->FILES[$sKey])) ? $this->FILES[$sKey] : $mDefault;
    }

    /**
     * Returns COOKIE data.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   Default return value
     *
     * @return  mixed   HTTP COOKIE value, default value or null
     */
    public function COOKIE ( $sKey, $mDefault = null )
    {
        return (isset($this->COOKIE[$sKey])) ? $this->COOKIE[$sKey] : $mDefault;
    }

    /**
     * Returns SESSION data.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   Default return value
     *
     * @return  mixed   HTTP SESSION value, default value or null
     */
    public function SESSION ( $sKey, $mDefault = null )
    {
        return (isset($this->SESSION[$sKey])) ? $this->SESSION[$sKey] : $mDefault;
    }

    /**
     * Returns true if data exists.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean Returns true if data exists
     */
    public function has ( $sKey )
    {
        return isset($this->POST[$sKey]) or isset($this->GET[$sKey]);
    }

    /**
     * Returns true if the request came from a secure connection.
     *
     * @return  boolean Returns true if the request came from a secure connection
     */
    public function secure ( /* void */ )
    {
        return array_element($this->SERVER, 'HTTPS', false) !== false;
    }
}
// END Logicoder_Request class

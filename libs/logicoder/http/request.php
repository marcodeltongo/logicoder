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
 * @link        http://www.logicoder.com/documentation/http_request.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_HTTP_Request extends Logicoder_OverArray
{
    /**
     * HTTP Request method.
     */
    private $method;

    /**#@+
     * HTTP DATA OBJECTS
     */
    public $GET;
    public $POST;
    public $META;
    public $FILES;
    public $COOKIE;
    /**#@-*/

    /**
     * Constructor.
     *
     * @param   string  $sClass         Filter class name
     * @param   boolean $bPreprocess    Preprocess or lazy ?
     */
    public function __construct ( $sClass, $bPreprocess )
    {
        /*
            Setup input filters, we initialize containers also for empty arrays.
        */
        $this->GET     = new $sClass('_GET',        $bPreprocess);
        $this->POST    = new $sClass('_POST',       $bPreprocess);
        $this->META    = new $sClass('_SERVER',     $bPreprocess);
        $this->FILES   = new $sClass('_FILES',      $bPreprocess);
        $this->COOKIE  = new $sClass('_COOKIE',     $bPreprocess);
        /*
            Method ?
        */
        $this->method = $this->META['REQUEST_METHOD'];
        /*
            Data ?
        */
        parent::__construct($this->{$this->method});
    }

    // -------------------------------------------------------------------------
    //  Request informations.
    // -------------------------------------------------------------------------

    /**
     * Returns request method.
     *
     * @return  string  Request method
     */
    public function method ( /* void */ )
    {
        return $this->method;
    }

    /**
     * Returns if this is an AJAX request.
     *
     * @return  boolean True if called with XMLHttpRequest, false otherwise
     */
    public function ajax ( /* void */ )
    {
        return $_SERVER['HTTP_X_REQUESTED_WITH'] === "XMLHttpRequest";
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

    // -------------------------------------------------------------------------
    //  Data getters.
    // -------------------------------------------------------------------------

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
}
// END Logicoder_HTTP_Request class

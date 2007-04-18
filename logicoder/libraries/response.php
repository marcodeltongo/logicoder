<?php
/**
 * Logicoder Web Application Framework - HTTP Response library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Should compress output ? If not required, no.
 */
if (!defined('RESPONSE_COMPRESS_OUTPUT'))
{
    define('RESPONSE_COMPRESS_OUTPUT', false);
}

// -----------------------------------------------------------------------------

/**
 * The HTTP response class.
 *
 * @package     Logicoder
 * @subpackage  HTTP
 * @link        http://www.logicoder.com/documentation/response.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Response extends Logicoder_OverArray
{
    /**
     * Constructor.
     */
    public function __construct ( /* void */ )
    {
        /*
            Get rid of previous output.
        */
        while (ob_get_level())
        {
            ob_end_clean();
        }
        /*
            Start collecting output.
        */
        ob_start();
    }

    /**
     * Output getter/setter.
     *
     * @param   string  $sOutput    Override output contents
     *
     * @return  string  Returns actual output buffer contents
     */
    public function output ( $sOutput = null )
    {
        if (is_string($sOutput))
        {
            ob_end_clean();
            ob_start();
            echo $sOutput;
        }
        return ob_get_contents();
    }

    /**
     * Set a cookie.
     *
     * @param   string  $sKey       Cookie key/name
     * @param   string  $sValue     Cookie value
     * @param   number  $nExpire    Cookie expiration
     * @param   string  $sDomain    Cookie domain
     * @param   string  $sPath      Cookie path
     * @param   string  $sPrefix    Cookie prefix
     *
     * @return  boolean Return success or failure
     */
    public function set_cookie ( $sKey, $sValue = '', $nExpire = '',
                                 $sDomain = '', $sPath = '/', $sPrefix = '')
    {
        /*
            Apply settings if needed.
        */
        if (empty($sPrefix) and defined('COOKIE_PREFIX'))
        {
            $sPrefix = COOKIE_PREFIX;
        }
        if (empty($sDomain) and defined('COOKIE_DOMAIN'))
        {
            $sDomain = COOKIE_DOMAIN;
        }
        if ($sPath == '/' and defined('COOKIE_PATH'))
        {
            $sPath = COOKIE_PATH;
        }
        /*
            Set expiration.
        */
        if (!is_numeric($nExpire))
        {
            $nExpire = time() - 86500;
        }
        else
        {
            if ($nExpire > 0)
            {
                $nExpire += time();
            }
            else
            {
                $nExpire = 0;
            }
        }
        return setcookie($sPrefix.$sKey, $sValue, $nExpire, $sPath, $sDomain, false);
    }

    /**
     * Delete a cookie.
     *
     * @param   string  $sKey       Cookie key/name
     * @param   string  $sDomain    Cookie domain
     * @param   string  $sPath      Cookie path
     * @param   string  $sPrefix    Cookie prefix
     *
     * @return  boolean Return success or failure
     */
    public function delete_cookie ( $sKey, $sDomain = '', $sPath = '/', $sPrefix = '')
    {
        return setcookie($sPrefix.$sKey, '', '', $sDomain, $sPath);
    }

    /**
     * Send an HTTP status response page.
     *
     * @param   mixed   $mHeader    Headers array or string
     * @param   string  $sView      View template to render
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function _http_error ( $mHeader, $sView, $sMessage = null )
    {
        /*
            Get rid of previous output.
        */
        while (ob_get_level())
        {
            ob_end_clean();
        }
        /*
            Send error header(s).
        */
        if (is_array($mHeader))
        {
            foreach ($mHeader as $h)
            {
                header($h);
            }
        }
        else
        {
            header($mHeader);
        }
        /*
            Use a view if available.
        */
        try
        {
            $oView = Logicoder()->load->view($sView);
            $oView->render((array)(is_null($sMessage) ? null : $sMessage));
        }
        catch (Exception $e)
        {
            echo "<strong>$mHeader</strong><br />";
            if (!is_null($sMessage))
            {
                echo "<em>&laquo; $sMessage &raquo;</em>";
            }
        }
        /*
            Done !
        */
        exit();
    }

    /**
     * Send an HTTP error 301 response.
     *
     * @param   string  $sURL       URL to redirect to
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function permanent ( $sURL, $sMessage = null )
    {
        $aHeaders = array('HTTP/1.1 301 Moved Permanently');
        $aHeaders[] = 'Location: ' . $sURL;
        $this->_http_error($aHeaders, 'errors/301', $sMessage);
    }

    /**
     * Send an HTTP error 302 response.
     *
     * @param   string  $sURL       URL to redirect to
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function redirect ( $sURL, $sMessage = null )
    {
        $aHeaders = array('HTTP/1.1 302 Found');
        $aHeaders[] = 'Location: ' . $sURL;
        $this->_http_error($aHeaders, 'errors/302', $sMessage);
    }

    /**
     * Send an HTTP error 304 response.
     *
     * @param   string  $sLastMod   Format like gmdate('D, d M Y H:i:s \G\M\T')
     */
    public function not_modified ( $sLastMod )
    {
        /*
            Create *unique* ETag.
        */
        $sETag = '"' . md5($sLastMod) . '"';
        /*
            Send headers.
        */
        header("Last-Modified: $sLastMod");
        header("ETag: $sETag");
        /*
            Check what browser sent.
        */
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
        {
            $mSince = stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        }
        else
        {
            $mSince = false;
        }
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
        {
            $mNoMatch = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
        }
        else
        {
            $mNoMatch = false;
            /*
                No values.
            */
            if ($mSince === false)
            {
                return false;
            }
        }
        /*
            Try to validate ETag.
        */
        if ($mNoMatch and $mNoMatch != $sETag)
        {
            /*
                ETag exists but doesn't match.
            */
            return false;
        }
        /*
            Try to validate IMS.
        */
        if ($mSince and $mSince != $sLastMod)
        {
            /*
                IMS exists but doesn't match.
            */
            return false;
        }
        /*
            No changes from last request, exit with a 304.
        */
        header('HTTP/1.1 304 Not Modified');
        exit();
    }

    /**
     * Send an HTTP error 401 response.
     *
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function unauthorized ( $sMessage = null )
    {
        $this->_http_error('HTTP/1.1 401 Unauthorized', 'errors/401', $sMessage);
    }

    /**
     * Send an HTTP error 403 response.
     *
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function forbidden ( $sMessage = null )
    {
        $this->_http_error('HTTP/1.1 403 Forbidden', 'errors/403', $sMessage);
    }

    /**
     * Send an HTTP error 404 response.
     *
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function not_found ( $sMessage = null )
    {
        $this->_http_error('HTTP/1.1 404 Not Found', 'errors/404', $sMessage);
    }

    /**
     * Send an HTTP error 405 response.
     *
     * @param   array   $aPermitted Array of permitted methods
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function not_allowed ( $aPermitted,  $sMessage = null )
    {
        $aHeaders = array('HTTP/1.1 405 Method Not Allowed');
        $aHeaders[] = 'Allow ' . implode(', ', $aPermitted);
        $this->_http_error($aHeaders, 'errors/405', $sMessage);
    }

    /**
     * Send an HTTP error 410 response.
     *
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function gone ( $sMessage = null )
    {
        $this->_http_error('HTTP/1.1 410 Gone', 'errors/410', $sMessage);
    }

    /**
     * Send an HTTP error 500 response.
     *
     * @param   string  $sMessage   Message/content to pass to view
     */
    public function server_error ( $sMessage = null )
    {
        $this->_http_error('HTTP/1.1 500 Internal Server Error', 'errors/500', $sMessage);
    }

    /**
     * Clean, filter and send output.
     */
    public function __destruct ( /* void */ )
    {
        $sContent = ob_get_clean();
        /*
            Setup compression ?
        */
        if (RESPONSE_COMPRESS_OUTPUT)
        {
            ob_start('ob_gzhandler');
        }
        /*
            Send contents.
        */
        echo $sContent;
        ob_end_flush();
    }
}
// END Logicoder_Response class

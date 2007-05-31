<?php
/**
 * Logicoder Web Application Framework - Sanitization Helpers
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/sanitize.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
define('SANITIZE_HELPER', true);

/**
 * Boolean constant for "magic quotes" handling
 */
define('MAGIC_QUOTES', (bool)get_magic_quotes_gpc());

// -----------------------------------------------------------------------------

/**
 * XSS attacks cleaning filter.
 *
 * Actually this is a rip-off of Christian Stocker work. ;)
 * @see http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function xss_filter ( $mValue )
{
    if (!is_string($mValue)) return $mValue;
    $mValue = (MAGIC_QUOTES) ? stripslashes($mValue) : $mValue;
    $mValue = str_replace( array("&amp;","&lt;","&gt;"), array("&amp;amp;","&amp;lt;","&amp;gt;",), $mValue);
    $mValue = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', '$1;', $mValue);
    $mValue = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', '$1$2;', $mValue);
    $mValue = html_entity_decode($mValue, ENT_COMPAT, 'UTF-8');
    $mValue = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', '$1>', $mValue);
    $mValue = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $mValue);
    $mValue = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $mValue);
    $mValue = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', '$1>', $mValue);
    $mValue = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU','$1>', $mValue);
    $mValue = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', '$1>', $mValue);
    $mValue = preg_replace('#</*\w+:\w[^>]*>#i', '', $mValue);
    do {
        $sOld = $mValue;
        $mValue = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', '', $mValue);
    } while ($sOld != $mValue);
    return $mValue;
}
// END sanitize function

// -----------------------------------------------------------------------------

/**
 * Alphanumeric sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_alphanum ( $mValue )
{
    return preg_replace('/[^\d\w\x80-\xff]/', '', $mValue);
}
// END sanitize_alphanum function

// -----------------------------------------------------------------------------

/**
 * Alpha sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_alpha ( $mValue )
{
    return preg_replace('/[^\w\x80-\xff]/', '', $mValue);
}
// END sanitize_alpha function

// -----------------------------------------------------------------------------

/**
 * Digits sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_digits ( $mValue )
{
    return preg_replace('/[^\d]/', '', $mValue);
}
// END sanitize_digits function

// -----------------------------------------------------------------------------

/**
 * Integer sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  integer Returns a sanitized integer
 */
function sanitize_int ( $mValue )
{
    /*
        Check for easy job...
    */
    if (!is_string($mValue) or is_numeric($mValue))
    {
        /*
            Double cast for scientific notation.
        */
        return (int) (float) $mValue;
    }
    /*
        Try to extract from string.
    */
    $mValue = (($mValue[0] == '-') ? '-' : '') . preg_replace('/[^0-9]/', '', $mValue);
    return (int) $mValue;
}
// END sanitize_int function

// -----------------------------------------------------------------------------

/**
 * Float sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  float   Returns a sanitized float
 */
function sanitize_float ( $mValue )
{
    /*
        Check for easy job...
    */
    if (!is_string($mValue) or is_numeric($mValue))
    {
        return (float) $mValue;
    }
    /*
        Try to extract from string.
    */
    $mValue = (($mValue[0] == '-') ? '-' : '') . preg_replace('/[^0-9.]/', '', $mValue);
    return (float) $mValue;
}
// END sanitize_float function

// -----------------------------------------------------------------------------

/**
 * Numeric string sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_numeric ( $mValue )
{
    return (string) sanitize_float($mValue);
}
// END sanitize_numeric function

// -----------------------------------------------------------------------------

/**
 * Boolean sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  boolean Returns a sanitized boolean
 */
function sanitize_boolean ( $mValue )
{
    /*
        Check for easy job...
    */
    if ($mValue === true or $mValue === false)
    {
        return $mValue;
    }
    /*
        Try to extract from string.
    */
    $mValue = strtolower(trim($mValue));
    if (in_array($mValue, array('1', 'on', 'true', 't', 'yes', 'y')))
    {
        return true;
    }
    elseif (in_array($mValue, array('0', 'off', 'false', 'f', 'no', 'n', '')))
    {
        return false;
    }
    return (boolean) $mValue;
}
// END sanitize_boolean function

// -----------------------------------------------------------------------------

/**
 * IP V4 address sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string with an IP address
 */
function sanitize_ipv4 ( $mValue )
{
    return long2ip(ip2long($mValue));
}
// END sanitize_ipv4 function

// -----------------------------------------------------------------------------

/**
 * ISO date sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_isodate ( $mValue )
{
    if (is_int($mValue) or (is_numeric($mValue) and $mValue == (int) $mValue))
    {
        return date('Y-m-d', $mValue);
    }
    return date('Y-m-d', strtotime($mValue));
}
// END sanitize_isodate function

// -----------------------------------------------------------------------------

/**
 * ISO time sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_isotime ( $mValue )
{
    if (is_int($mValue) or (is_numeric($mValue) and $mValue == (int) $mValue))
    {
        return date('H:i:s', $mValue);
    }
    return date('H:i:s', strtotime($mValue));
}
// END sanitize_isotime function

// -----------------------------------------------------------------------------

/**
 * ISO date and time sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string
 */
function sanitize_isodatetime ( $mValue )
{
    if (is_int($mValue) or (is_numeric($mValue) and $mValue == (int) $mValue))
    {
        return date(DATE_ISO8601, $mValue);
    }
    return date(DATE_ISO8601, strtotime($mValue));
}
// END sanitize_isodatetime function

// -----------------------------------------------------------------------------

/**
 * Path sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string with the real path
 */
function sanitize_path ( $mValue )
{
    return realpath($mValue);
}
// END sanitize_path function

// -----------------------------------------------------------------------------

/**
 * Dirname sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string with the dir name
 */
function sanitize_dir ( $mValue )
{
    return dirname($mValue);
}
// END sanitize_dir function

// -----------------------------------------------------------------------------

/**
 * Filename sanitization.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string with the base file name
 */
function sanitize_file ( $mValue )
{
    return basename($mValue);
}
// END sanitize_file function

// -----------------------------------------------------------------------------

/**
 * Sanitize a string for system() or similar functions.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized string for system() or similar functions
 */
function sanitize_system ( $mValue )
{
    $rPattern = '/(;|\||`|>|<|&|^|"|'."\n|\r|'".'|{|}|[|]|\)|\()/i';
    $mValue = preg_replace($rPattern, '', $mValue);
    $mValue = '"' . preg_replace('/\$/', '\\\$', $mValue) . '"';
    return $mValue;
}
// END sanitize_system function

// -----------------------------------------------------------------------------

/**
 * Sanitize a string for SQL.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized SQL string
 */
function sanitize_sql ( $mValue )
{
    $mValue = (MAGIC_QUOTES) ? $mValue : addslashes($mValue);
    $rPattern = "/;/";
    return preg_replace($rPattern, '', $mValue);
}
// END sanitize_sql function

// -----------------------------------------------------------------------------

/**
 * Sanitize an HTML string.
 *
 * @param   string  $mValue String to sanitize
 *
 * @return  string  Returns a sanitized HTML string
 */
function sanitize_html ( $mValue )
{
    $aPatterns = array('/\&/', '/</', '/>/', '/\n/', '/"/', "/'/", "/%/", '/\(/', '/\)/', '/\+/', '/-/');
    $aReplaces = array('&amp;', '&lt;', '&gt;', '<br />', '&quot;', '&#39;', '&#37;', '&#40;', '&#41;', '&#43;', '&#45;');
    return preg_replace($aPatterns, $aReplaces, $mValue);
}
// END sanitize_html function

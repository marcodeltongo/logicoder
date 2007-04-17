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
 * Boolean constant for "magic quotes" handling
 */
define('MAGIC_QUOTES', (bool)get_magic_quotes_gpc());

// -----------------------------------------------------------------------------

/**
 * Basic cleaning.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize ( $sString )
{
    $sString = (MAGIC_QUOTES) ? stripslashes($sString) : $sString;
    $sString = str_replace( array("&amp;","&lt;","&gt;"), array("&amp;amp;","&amp;lt;","&amp;gt;",), $sString);
    $sString = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', '$1;', $sString);
    $sString = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', '$1$2;', $sString);
    $sString = html_entity_decode($sString, ENT_COMPAT, 'UTF-8');
    $sString = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', '$1>', $sString);
    $sString = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $sString);
    $sString = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $sString);
    $sString = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', '$1>', $sString);
    $sString = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU','$1>', $sString);
    $sString = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', '$1>', $sString);
    $sString = preg_replace('#</*\w+:\w[^>]*>#i', '', $sString);
    do {
        $sOld = $sString;
        $sString = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', '', $sString);
    } while ($sOld != $sString);
    return $sString;
}
// END sanitize function

/**
 * Alphanumeric sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_alphanum ( $sString )
{
    return preg_replace('/[^[:alnum:]]/', '', $sString);
}
// END sanitize_alphanum function

/**
 * Alpha sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_alpha ( $sString )
{
    return preg_replace('/[^[:alpha:]]/', '', $sString);
}
// END sanitize_alpha function

/**
 * Integer sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_int ( $sString )
{
    return (int)$sString;
}
// END sanitize_int function

/**
 * Digits sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_digits ( $sString )
{
    return preg_replace('/[^\d]/', '', $sString);
}
// END sanitize_digits function

/**
 * Float sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_float ( $sString )
{
    return (float)$sString;
}
// END sanitize_float function

/**
 * Path sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_path ( $sString )
{
    return realpath($sString);
}
// END sanitize_path function

/**
 * Dirname sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_dir ( $sString )
{
    return dirname($sString);
}
// END sanitize_dir function

/**
 * Filename sanitization.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_file ( $sString )
{
    return basename($sString);
}
// END sanitize_file function

/**
 * Sanitize a string for system() or similar functions.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_system ( $sString )
{
    $rPattern = '/(;|\||`|>|<|&|^|"|'."\n|\r|'".'|{|}|[|]|\)|\()/i';
    $sString = preg_replace($rPattern, '', $sString);
    $sString = '"' . preg_replace('/\$/', '\\\$', $sString) . '"';
    return $sString;
}
// END sanitize_system function

/**
 * Sanitize a string for SQL.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_sql ( $sString )
{
    $sString = (MAGIC_QUOTES) ? $sString : addslashes($sString);
    $rPattern = "/;/";
    return preg_replace($rPattern, '', $sString);
}
// END sanitize_sql function

/**
 * Sanitize a string for LDAP.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_ldap ( $sString )
{
    return preg_replace('/(\)|\(|\||&)/', '', $sString);
}
// END sanitize_ldap function

/**
 * Sanitize an HTML string.
 *
 * @param   string  $sString    String to sanitize
 */
function sanitize_html ( $sString )
{
    $aPatterns = array('/\&/', '/</', '/>/', '/\n/', '/"/', "/'/", "/%/", '/\(/', '/\)/', '/\+/', '/-/');
    $aReplaces = array('&amp;', '&lt;', '&gt;', '<br />', '&quot;', '&#39;', '&#37;', '&#40;', '&#41;', '&#43;', '&#45;');
    return preg_replace($aPatterns, $aReplaces, $sString);
}
// END sanitize_html function

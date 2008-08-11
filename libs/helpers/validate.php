<?php
/**
 * Logicoder Web Application Framework - Validation Helpers
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
define('VALIDATE_HELPER', true);

// -----------------------------------------------------------------------------

/**
 * Validates a blank string.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_blank ( $sValue )
{
    return (!is_string($sValue) and !is_null($sValue)) ? false : trim($sValue) == '';
}
// END validate_blank function

// -----------------------------------------------------------------------------

/**
 * Validates a string being between min and max long.
 *
 * @param   string  $sValue String to validate
 * @param   integer $iMin   Minimum length
 * @param   integer $iMax   Maximum length
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_length ( $sValue, $iMin = 0, $iMax = 255 )
{
    $iLen = strlen($sValue);
    return ($iLen >= $iMin and $iLen <= $iMax);
}
// END validate_length function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing only letters and numbers.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_alphanum ( $sValue )
{
    return !preg_match('/[^\d\w\x80-\xff]/', $sValue);
}
// END validate_alphanum function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing only letters.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_alpha ( $sValue )
{
    return !preg_match('/[^\w\x80-\xff]/', $sValue);
}
// END validate_alpha function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing only numbers.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_digits ( $sValue )
{
    return !preg_match('/[^\d]/', $sValue);
}
// END validate_digits function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing a boolean value.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_boolean ( $sValue )
{
    $sValue = strtolower(trim($sValue));
    if (in_array($sValue, array('1', 'on', 'true', 't', 'yes', 'y')))
    {
        return true;
    }
    elseif (in_array($sValue, array('0', 'off', 'false', 'f', 'no', 'n', '')))
    {
        return false;
    }
    return (boolean) $sValue;
}
// END validate_boolean function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing an integer value.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_int ( $sValue )
{
    return is_numeric($sValue) and ($sValue == (int) $sValue);
}
// END validate_int function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing a float value.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_float ( $sValue )
{
    return is_numeric($sValue) and ($sValue == (float) $sValue);
}
// END validate_float function

// -----------------------------------------------------------------------------

/**
 * Validates a string being between min and max long.
 *
 * @param   string  $sValue     String to validate
 * @param   integer $iDigits    Maximum integer part digits
 * @param   integer $iDecimals  Maximum floating part decimals
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_numeric ( $sValue, $iDigits = 10, $iDecimals = 2 )
{
    if ($iDecimals == 0)
    {
        $re = '/^[\d]{0,' . $iDigits . '}$/D';
    }
    else
    {
        $re = '/^[\d]{0,' . $iDigits . '}.[\d]{0,' . $iDecimals . '}$/D';
    }
    return (boolean) preg_match($re, $sValue);
}
// END validate_numeric function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing an IP address V4.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_ipv4 ( $sValue )
{
    $ip = ip2long($sValue);
    return !($ip == -1 or $ip === false);
}
// END validate_ipv4 function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing an ISO date.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_isodate ( $sValue )
{
    return (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/D', $sValue, $aMatches)
            and checkdate($aMatches[2],$aMatches[3],$aMatches[1]));
}
// END validate_isodate function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing an ISO time.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_isotime ( $sValue )
{
    return (preg_match('/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]:[0-5][0-9]$/D', $sValue)
            or ($sValue == '24:00:00'));
}
// END validate_isotime function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing an ISO datetime.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_isodatetime ( $sValue )
{
    return ((strlen($sValue) == 19)
            and validate_isodate(substr($sValue, 0, 10))
            and in_array(substr($sValue, 10, 1), array('T', ' '))
            and validate_isotime(substr($sValue, 11, 8)));
}
// END validate_isodatetime function

// -----------------------------------------------------------------------------

/**
 * Validates a string containing an email address.
 *
 * @param   string  $sValue String to validate
 *
 * @return  boolean Returns TRUE on SUCCESS or FALSE on FAILURE
 */
function validate_email ( $sValue )
{
    return (boolean)preg_match('/^([\w\d\x80-\xff_.+-])+@(([\w\d\x80-\xff-])+.)+([\w\d\x80-\xff]{2,4})+$/D', $sValue);
}
// END validate_email function

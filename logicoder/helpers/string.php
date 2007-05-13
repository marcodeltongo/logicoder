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
define('STRING_HELPER', true);

// -----------------------------------------------------------------------------

/**
 * Reverse of nl2br.
 *
 * @param   string  $sString    String to filter
 *
 * @return  string  Returns a string with <br>'s converted to newlines
 */
function br2nl ( $sString )
{
    return preg_replace("=<br */?>=i", "\n",
                        preg_replace("/(\r\n|\n|\r)/", "", $sString));
}
// END br2nl function

// -----------------------------------------------------------------------------

/**
 * Crops text at length, preserve the last word, and adds "&hellip;".
 *
 * @param   string  $sString    String to filter
 * @param   int     $nLength    String length limit
 * @param   boolean $bLastWord  Should last word be preserved ?
 *
 * @return  string  Returns a cropped string
 */
function add_dots ( $sString, $nLength, $bLastWord = true )
{
    if (strlen($sString) > $nLength)
    {
        if ($bLastWord)
        {
            $sString = substr($sString, 0, $nLength);
            $lastspace = strrpos($sString, " ");
            $sString = substr($sString, 0, $lastspace);
        }
        else
        {
            $sString = substr($sString, 0, $nLength);
        }
        $sString .= '&hellip;';
    }
    return $sString;
}
// END add_dots function

// -----------------------------------------------------------------------------

/**
 * Trim slashes.
 *
 * @param   string  $sString    String to filter
 *
 * @return  string  Returns slash-trimmed string
 */
function trim_slashes ( $sString )
{
	return preg_replace('|^/*(.+?)/*$|', '$1', $sString);
}
// END trim_slashes function

// -----------------------------------------------------------------------------

/**
 * Converts double slashes to single except for those after ":" like in http://
 *
 * @param   string  $sString    String to filter
 *
 * @return  string  Returns filtered string
 */
function reduce_double_slashes ( $sString )
{
	return preg_replace("|([^:])//+|", "$1/", $sString);
}
// END reduce_double_slashes function

// -----------------------------------------------------------------------------

/**
 * Alternates strings.
 *
 * @param   mixed   $parameter  Strings to alternate
 *
 * @return  string  Returns one string from the passed ones
 */
function alternate ( /* ... */ )
{
    static $iC;
    /*
        What has been passed ?
    */
    switch (func_num_args())
    {
        case 0:
            $iC = 0;
            return '';
        break;

        case 1:
            $aArgs = func_get_arg(0);
        break;

        default:
            $aArgs = func_get_args();
    }
    return $aArgs[($iC++ % count($aArgs))];
}
// END alternate function

// -----------------------------------------------------------------------------

/**
 * Returns a random string
 *
 * @param   string  $sPool      Set of chars to use
 * @param   integer $iLength    Length of the string
 *
 * @return  string  Generated string
 */
function random_string ( $sPool, $iLength )
{
    $sGen = '';
    for ( $iC = 0; $iC < $iLength; ++$iC )
	{
        $sGen .= substr($sPool, mt_rand(0, strlen($sPool) - 1), 1);
	}
    return $sGen;
}
// END random_string function

// -----------------------------------------------------------------------------

/**
 * Returns a random non-numeric string
 *
 * @param   integer $iLength    Length of the string
 *
 * @return  string  Generated string
 */
function random_alpha_string ( $iLength )
{
    return random_string($iLength, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
}
// END random_alpha_string function

// -----------------------------------------------------------------------------

/**
 * Returns a random alphanumeric string
 *
 * @param   integer $iLength    Length of the string
 *
 * @return  string  Generated string
 */
function random_alnum_string ( $iLength )
{
    return random_string($iLength, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
}
// END random_alnum_string function

// -----------------------------------------------------------------------------

/**
 * Returns a random unique string
 *
 * @param   integer $iLength    Length of the string
 *
 * @return  string  Generated string
 */
function random_unique_string ( $iLength )
{
    return md5(uniqid(mt_rand()));
}
// END random_unique_string function

// -----------------------------------------------------------------------------

/**
 * Returns a singular version of a word
 *
 * Doesn't support irregular or non-english words.
 *
 * @param   string  $sWord      Word to make singular
 *
 * @return  string  Generated string
 */
function str_singular ( $sWord )
{
    if (is_string($sWord))
    {
        if (substr_compare($sWord, 'ies', -3, 3, true) === 0)
        {
            return substr($sWord, 0, -3) . 'y';
        }
        elseif (substr_compare($sWord, 's', -1, 1, true) === 0)
        {
            return substr($sWord, 0, -1);
        }
    }
    return $sWord;
}
// END str_singular function

// -----------------------------------------------------------------------------

/**
 * Returns a plural version of a word.
 *
 * Doesn't support irregular or non-english words.
 *
 * @param   string  $sWord      Word to make plural
 *
 * @return  string  Generated string
 */
function str_plural ( $sWord )
{
    if (is_string($sWord))
    {
        if (substr_compare($sWord, 'y', -1, 1, true) === 0)
        {
            return substr($sWord, 0, -1) . 'ies';
        }
        elseif(substr_compare($sWord, 's', -1, 1, true) !== 0)
        {
            return $sWord . 's';
        }
    }
    return $sWord;
}
// END str_plural function

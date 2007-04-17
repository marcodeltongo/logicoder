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
 * Reverse of nl2br.
 *
 * @param   string  $sString    String to filter
 */
function br2nl ( $sString )
{
    return preg_replace("=<br */?>=i", "\n",
                        preg_replace("/(\r\n|\n|\r)/", "", $sString));
}
// END br2nl function

/**
 * Crops text at length, preserve the last word, and adds "&hellip;".
 *
 * @param   string  $sString    String to filter
 * @param   int     $nLength    String length limit
 * @param   boolean $bLastWord  Should last word be preserved ?
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

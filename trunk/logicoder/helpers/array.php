<?php
/**
 * Logicoder Web Application Framework - Array Helpers
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/array.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Returns array elements or default value if not set / empty.
 *
 * @param   string  $sNeedle    Element key
 * @param   array   $aHaystack  Array stack
 * @param   mixed   $mDefault   Optional default value
 */
function array_element ( $sNeedle, $aHaystack, $mDefault = null )
{
    if (isset($aHaystack[$sNeedle]) and $aHaystack[$sNeedle] !== '')
    {
        return $aHaystack[$sNeedle];
    }
    return $mDefault;
}
// END array_element function

/**
 * Returns a random element of the passed array.
 *
 * @param   array   $aHaystack  Array stack
 */
function array_random ( $aHaystack )
{
    if (is_array($aHaystack))
    {
        return $aHaystack[array_rand($aHaystack)];
    }
    return $aHaystack;
}
// END array_random function

/**
 * Returns true if an array is an associative array.
 *
 * @param   array   $aArray     Array to check
 */
function is_assoc ( $aArray )
{
    if (is_array($aArray))
    {
        return (bool)array_diff_assoc(array_keys($aArray), range(0, count($aArray)));
	}
	return false;
}
// END is_assoc function

/**
 * Returns an array with the object variables.
 *
 * @param   object  $oSrc       Object to get vars from
 */
function object_to_array ( $oSrc )
{
    if (!is_object($oSrc))
    {
        return $oSrc;
    }
    /*
        If object has __toArray method, use it.
    */
    if (method_exists($oSrc, '__toArray'))
    {
        return $oSrc->__toArray();
    }
    /*
        Loop through object vars.
    */
    $aRet = array();
    foreach (get_object_vars($oSrc) as $k => $v)
    {
        if (is_scalar($v))
        {
            $aRet[$k] = $v;
        }
    }
    return $aRet;
}
// END object_to_array function

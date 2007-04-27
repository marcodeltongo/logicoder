<?php
/**
 * Logicoder Web Application Framework - Debug Helpers
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/debug.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
define('DEBUG_HELPER', true);

/**#@+
 * Required dependency.
 */
if (!defined('HTML_HELPER'))
{
    if (defined('LOGICODER'))
    {
        Logicoder::instance()->load->helper('HTML');
    }
    else
    {
        require('html.php');
    }
}
/**#@-*/

// -----------------------------------------------------------------------------

/**
 * Pretty prints memory usage.
 */
function memory_usage ( /* void */ )
{
    if (function_exists('memory_get_usage'))
    {
        return round(memory_get_usage()/1024/1024, 2).'mb';
    }
    return 'N/A';
}
// END memory_usage function

// -----------------------------------------------------------------------------

/**
 * Simple wrapper which adds "pre" tags to var_export().
 *
 * @param   mixed   $parameter  Zero or more parameters to be exported
 */
function pre_dump ( /* ... */ )
{
    foreach (func_get_args() as $mVar)
    {
        echo '<pre>' . var_export($mVar, true) . '</pre>';
    }
}
// END pre_dump function

// -----------------------------------------------------------------------------

/**
 * Simple wrapper which adds "pre" tags to var_export() and exits.
 *
 * @param   mixed   $parameter  Zero or more parameters to be exported
 */
function pre_dump_exit ( /* ... */ )
{
    foreach (func_get_args() as $mVar)
    {
        echo '<pre>' . var_export($mVar, true) . '</pre>';
    }
    exit();
}
// END pre_dump_exit function

// -----------------------------------------------------------------------------

/**
 * Returns variable type.
 *
 * @param   mixed   $mVar   Variable to check
 *
 * @return  string  The variable type
 */
function get_type ( $mVar )
{
    if (is_null($mVar))
    {
        /*
            It's null, work done.
        */
        return 'null';
    }
    elseif (is_scalar($mVar))
    {
        /*
            It's scalar, so may be integer, float, boolean or string.
        */
        if (is_integer($mVar))
        {
            return 'integer';
        }
        elseif (is_float($mVar))
        {
            return 'float';
        }
        elseif (is_bool($mVar))
        {
            return 'boolean';
        }
        else
        {
            return 'string';
        }
        return 'scalar';
    }
    else
    {
        /*
            It's not scalar, so may be array, object or resource.
        */
        if (is_array($mVar))
        {
            return 'array';
        }
        elseif (is_object($mVar))
        {
            return 'object';
        }
        elseif (is_resource($mVar))
        {
            return 'resource';
        }
        return 'not_scalar';
    }
}
// END get_type function

// -----------------------------------------------------------------------------

/**
 * Returns a variable name.
 *
 * @param   mixed   $mVar       Variable to search
 * @param   string  $sFunction  Function name to scan
 *
 * @return  string  Var name if found
 */
function var_name ( $mVar, $sFunction = 'var_name' )
{
    $aBT = array_reverse(debug_backtrace());
    $aIn = array('include', 'include_once', 'require', 'require_once');
    /*
        Look at last included file for line numbers.
    */
    foreach ($aBT as $aTrace)
    {
        if (!(isset($aTrace['function']) and
            (in_array($aTrace['function'], $aIn) or
             (strcmp($aTrace['function'], $sFunction) !== 0))))
        {
            $aFile = $aTrace;
        }
    }
    /*
        Get the name.
    */
    if (isset($aFile))
    {
        $aLines = file($aFile['file']);
        $sCode  = $aLines[--$aFile['line']];
        /*
            Find function call.
        */
        preg_match('|\b'.$sFunction.'\s*\(\s*([^()]+)\s*|', $sCode, $aMatches);
        return $aMatches[1];
    }
    return '';
}
// END var_name function

// -----------------------------------------------------------------------------

/**
 * Dumps with details.
 *
 * @param   mixed   $mVar       Variable to dump
 * @param   boolean $bReturn    Whether to return or output
 *
 * @return  string  If $bReturn == true
 */
function dump ( $mVar, $bReturn = false )
{
    $sOutput = '';
    if (is_scalar($mVar))
    {
        /*
            Prepare header.
        */
        $aData = array(array(var_name($mVar, 'dump')));
        /*
            Get value.
        */
        ob_start();
        debug_zval_dump($mVar);
        $aData[] = array('<pre>' . ob_get_clean() . '</pre>');
        /*
            Dump table.
        */
        $sOutput = array2table($aData, null, 'dump', true);
    }
    else
    {
        /*
            Prepare header.
        */
        $aData = array(array(var_name($mVar, 'dump')));
        /*
            Get value.
        */
        ob_start();
        debug_zval_dump($mVar);
        $aData[] = array('<pre>' . ob_get_clean() . '</pre>');
        /*
            Dump table.
        */
        $sOutput = array2table($aData, null, 'dump', true);
    }
    if ($bReturn)
    {
        return $sOutput;
    }
    echo $sOutput;
}
// END dump function
/*
dump(0);
dump(true);
dump(false);

$test = 'YEP!';
dump($test);

class Vegetable {

   var $edible;
   var $color;
   var $test = array(1, 2, 3, array('0'));
   var $t;

   function Vegetable($edible, $color="green") {
       $this->edible = $edible;
       $this->color = $color;
       $this->t = $this;
   }

   function is_edible() {
       return $this->edible;
   }

   function what_color() {
       return $this->color;
   }

}
$variable=new Vegetable("spinach");
dump($variable);
*/

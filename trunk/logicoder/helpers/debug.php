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
 * Simple wrapper which adds "pre" tags to var_export().
 *
 * @param   mixed   $parameter  Zero or more parameters to be exported
 */
function dump ( /* ... */ )
{
    foreach (func_get_args() as $mVar)
    {
        echo '<pre>' . var_export($mVar, true) . '</pre>';
    }
}
// END dump function

/**
 * Simple wrapper which adds "pre" tags to var_export() and exits.
 *
 * @param   mixed   $parameter  Zero or more parameters to be exported
 */
function exit_dump ( /* ... */ )
{
    foreach (func_get_args() as $mVar)
    {
        echo '<pre>' . var_export($mVar, true) . '</pre>';
    }
    exit();
}
// END exit_dump function

/**
 * Pretty prints memory usage.
 */
function memory_usage ( /* void */ )
{
    return (function_exists('memory_get_usage')) ? round(memory_get_usage()/1024/1024, 2).'mb' : 'N/A';
}
// END memory_usage function

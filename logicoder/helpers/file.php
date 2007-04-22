<?php
/**
 * Logicoder Web Application Framework - File Helpers
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/file.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
define('FILE_HELPER', true);

// -----------------------------------------------------------------------------

/**
 * Returns the file extension.
 *
 * @param   string  $sFilename  The file name to extract extension from
 *
 * @return  string  Returns the file extension
 */
function file_ext ( $sFilename )
{
    return @end(explode('.', $sFilename));
}
// END file_ext function

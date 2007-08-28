<?php
/**
 * Logicoder Web Application Framework - CSS and JS GZipper
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/gzipper.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Define the file extension used for PHP files.
 * AUTODETECT: '.' . pathinfo(__FILE__, PATHINFO_EXTENSION)
 */
define('EXT', '.' . pathinfo(__FILE__, PATHINFO_EXTENSION));

/*
    Load main settings.
*/
require 'settings' . EXT;

/*
    Start compression.
*/
ob_start ("ob_gzhandler");

/*
    Cache for 1 day or 86400 seconds.
*/
header("Cache-Control: must-revalidate");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 86400) . " GMT");

/*
    Send correct header.
*/
header("Content-type: text/javascript; charset: UTF-8");

/*
    Iterate on requested files.
*/
foreach (array_keys($_GET) as $sFilename)
{
    /*
        Simple security measure.
        Only (utf8) alphanumeric or forward slash accepted.
    */
    $sFilename = preg_replace('/[^\d\w\x80-\xff\/]/', '', $sFilename);

    /*
        Spit out the contents...
    */
    @readfile(MEDIA_ROOT . 'js/' . $sFilename . '.js', false);
}
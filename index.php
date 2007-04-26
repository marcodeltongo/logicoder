<?php
/**
 * Logicoder Web Application Framework - Front Runner
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Startup time for benchmarking.
 */
define('BENCHMARK_STARTUP', microtime(true));

/**
 * Define the file extension used for PHP files.
 * DEFAULT: '.' . pathinfo(__FILE__, PATHINFO_EXTENSION)
 */
define('EXT', '.' . pathinfo(__FILE__, PATHINFO_EXTENSION));

/*
    Load main settings.
*/
require 'settings' . EXT;

/*
    Load the framework.
*/
require LOGICODER_ROOT . 'logicoder' . EXT;

/*
    Run the system.
*/
Logicoder::instance();

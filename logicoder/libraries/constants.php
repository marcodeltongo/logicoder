<?php
/**
 * Logicoder Web Application Framework - Constants
 *
 * @package     Logicoder
 * @subpackage  Core
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------
/*
    Logicoder_Logger constants
*/

/**#@+
 * Defines a logger type.
 */
define('DUMMY_LOGGER',      'Dummy');
define('MAIL_LOGGER',       'Mail');
define('FILE_LOGGER',       'File');
define('DATEFILE_LOGGER',   'DateFile');
define('COMMENT_LOGGER',    'Comment');
define('CONSOLE_LOGGER',    'Console');
define('FIREBUG_LOGGER',    'Firebug');
/**#@-*/

/*
    The loggers use the syslog priorities as levels.
    see http://www.php.net/manual/en/function.syslog.php

    LOG_EMERG   system is unusable
    LOG_ALERT   action must be taken immediately
    LOG_CRIT    critical conditions
    LOG_ERR     error conditions
    LOG_WARNING warning conditions
    LOG_NOTICE  normal, but significant, condition
    LOG_INFO    informational message
    LOG_DEBUG   debug-level message
*/
/**#@+
 * Aliases for syslog logger levels.
 */
define('LOG_ERROR',     LOG_ERR);
define('LOG_CRITICAL',  LOG_CRIT);
define('LOG_EMERGENCY', LOG_EMERG);
/**#@-*/

// -----------------------------------------------------------------------------
/*
    Logicoder_View_* constants
*/

/**#@+
 * View source types.
 */
define('VIEW_FROM_FILE',    1);
define('VIEW_FROM_QUERY',   2);
define('VIEW_FROM_PARAM',   4);
/**#@-*/

/**#@+
 * View source markup language.
 */
define('VIEW_IS_HTML',      32);
define('VIEW_IS_PHP',       64);
define('VIEW_IS_TEMPLATE',  128);
define('VIEW_IS_DWT',       256);
/**#@-*/

/**#@+
 * View types. (BIT-FIELDS)
 */
define('HTML_VIEW',         VIEW_FROM_FILE | VIEW_IS_HTML);
define('PHP_VIEW',          VIEW_FROM_FILE | VIEW_IS_PHP);
define('TEMPLATE_VIEW',     VIEW_FROM_FILE | VIEW_IS_TEMPLATE);
define('DWT_VIEW',          VIEW_FROM_FILE | VIEW_IS_DWT);
/**#@-*/

/**#@+
 * View type extensions.
 */
define('HTML_VIEW_MASK', '{.html, .htm, .xhtml}');
define('PHP_VIEW_MASK',  '{.php, .php5, .inc}');
define('TEMPLATE_VIEW_MASK',  '{.thmtl, .tpl, .tmpl, .tml}');
define('DWT_VIEW_MASK',  '{.dwt}');
/**#@-*/

// -----------------------------------------------------------------------------
/*
    Logicoder_Cache_* constants
*/

/**#@+
 * Cache containers.
 */
define('CACHE_FILE',        'File');
define('CACHE_MEMCACHE',    'Memcache');
define('CACHE_APC',         'APC');
/**#@-*/

// -----------------------------------------------------------------------------
/*
    Logicoder_DB_* constants
*/

/**#@+
 * Database statements fetching modes.
 */
define('DB_FETCH_ASSOC',    1);
define('DB_FETCH_NUM',      2);
define('DB_FETCH_OBJ',      3);
/**#@-*/

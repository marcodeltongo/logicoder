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
    Define log types.

    Starts with the error_log() types, see http://php.net/manual/en/function.error-log.php
*/

define('MAIL_LOGGER',     1);
define('FILE_LOGGER',     3);
define('DATEFILE_LOGGER', 4);
define('COMMENT_LOGGER',  5);
define('WINDOW_LOGGER',   6);
define('CONSOLE_LOGGER',  7);
define('DUMMY_LOGGER',    8);

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

define('LOG_ERROR',     LOG_ERR);
define('LOG_CRITICAL',  LOG_CRIT);
define('LOG_EMERGENCY', LOG_EMERG);

// END Logicoder_Logger constants

// -----------------------------------------------------------------------------

/*
    Database statements results modes.
*/
define('DB_FETCH_ASSOC',    1);
define('DB_FETCH_NUM',      2);
define('DB_FETCH_OBJ',      3);

// END Logicoder_DB_* constants

// -----------------------------------------------------------------------------

/*
    Define view source type.
*/
define('VIEW_FROM_FILE',    1);
define('VIEW_FROM_QUERY',   2);
define('VIEW_FROM_PARAM',   4);
#define('VIEW_FROM_CACHE',   8);

/*
    Define view source kind.
*/
define('VIEW_IS_HTML',      32);
define('VIEW_IS_PHP',       64);
define('VIEW_IS_TEMPLATE',  128);
define('VIEW_IS_TEXTILE',   256);
#define('VIEW_IS_MARKDOWN',  512);

/*
    Define view types. (BIT-FIELDS)
*/
define('HTML_VIEW',         VIEW_FROM_FILE | VIEW_IS_HTML);
define('PHP_VIEW',          VIEW_FROM_FILE | VIEW_IS_PHP);
define('TEMPLATE_VIEW',     VIEW_FROM_FILE | VIEW_IS_TEMPLATE);
define('TEXTILE_VIEW',      VIEW_FROM_FILE | VIEW_IS_TEMPLATE);

/*
    Define type extensions.
*/
define('HTML_VIEW_MASK', '{.html, .htm, .xhtml}');
define('PHP_VIEW_MASK',  '{.php, .php5, .inc}');
define('TEMPLATE_VIEW_MASK',  '{.tpl, .tmpl, .tml}');
define('TEXTILE_VIEW_MASK',  '{.txl, .textile}');

// END Logicoder_View_* constants

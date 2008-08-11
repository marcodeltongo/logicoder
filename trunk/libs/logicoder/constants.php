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
define('VIEW_IS_PHP',       16);
define('VIEW_IS_CLASS',     32);
define('VIEW_IS_HTML',      64);
define('VIEW_IS_TEMPLATE',  128);
define('VIEW_IS_DWT',       256);
/**#@-*/

/**#@+
 * View types. (BIT-FIELDS)
 */
define('PHP_VIEW',          VIEW_FROM_FILE | VIEW_IS_PHP);
define('PROXY_VIEW',        VIEW_FROM_FILE | VIEW_IS_CLASS);
define('HTML_VIEW',         VIEW_FROM_FILE | VIEW_IS_HTML);
define('TEMPLATE_VIEW',     VIEW_FROM_FILE | VIEW_IS_TEMPLATE);
define('DWT_VIEW',          VIEW_FROM_FILE | VIEW_IS_DWT);
/**#@-*/

/**#@+
 * View type extensions.
 */
define('HTML_VIEW_MASK',        '{.html, .htm, .xhtml}');
define('PHP_VIEW_MASK',         '{.php, .php5, .inc}');
define('TEMPLATE_VIEW_MASK',    '{.thmtl, .tpl, .tmpl, .tml}');
define('DWT_VIEW_MASK',         '{.dwt}');
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

// ------------------------------------------------------------------------------
/*
    Logicoder_Model_* constants
*/

/**
 * Define fields class prefix.
 */
if (!defined('MODEL_FIELD_PREFIX'))
{
    define('MODEL_FIELD_PREFIX', 'Logicoder_Model_Field_');
}
/**
 * Define fields class suffix.
 */
if (!defined('MODEL_FIELD_SUFFIX'))
{
    define('MODEL_FIELD_SUFFIX', '');
}

/**
 * Define relations class prefix.
 */
if (!defined('MODEL_RELATION_PREFIX'))
{
    define('MODEL_RELATION_PREFIX', 'Logicoder_Model_Relation_');
}
/**
 * Define relations class suffix.
 */
if (!defined('MODEL_RELATION_SUFFIX'))
{
    define('MODEL_RELATION_SUFFIX', '');
}

/**
 * Define one to one relations class.
 */
if (!defined('MODEL_RELATION_ONETOONE'))
{
    define('MODEL_RELATION_ONETOONE', MODEL_RELATION_PREFIX . 'OneToOne' . MODEL_RELATION_SUFFIX);
}
/**
 * Define many to one relations class.
 */
if (!defined('MODEL_RELATION_MANYTOONE'))
{
    define('MODEL_RELATION_MANYTOONE', MODEL_RELATION_PREFIX . 'ManyToOne' . MODEL_RELATION_SUFFIX);
}
/**
 * Define many to many relations class.
 */
if (!defined('MODEL_RELATION_MANYTOMANY'))
{
    define('MODEL_RELATION_MANYTOMANY', MODEL_RELATION_PREFIX . 'ManyToMany' . MODEL_RELATION_SUFFIX);
}

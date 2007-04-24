<?php if (!isset($this)) { header("HTTP/1.0 404 Not Found"); die(); }
/**
 * Logicoder Web Application Framework - Project applications settings
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/configuration.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/*
    The applications of the project and their sub-path.
*/
$this['INSTALLED_APPS'] = array('default' => 'default');

/*
    Optional suffix to add to URLs, for example '.html' or blank.
*/
$this['URL_SUFFIX'] = '';

// -----------------------------------------------------------------------------

/*
    Load database settings.
*/
$this->load('db_settings' . EXT);

// -----------------------------------------------------------------------------

/*
    Internationalization settings.
*/
$this->namespace('I18N', true);

/*
    Load internationalization libraries as required.
*/
$this['ACTIVE'] = true;

/*
    Default language to use if not elsewhere defined.
*/
$this['DEFAULT'] = 'it';

/*
    All available languages.
*/
$this['AVAILABLE'] = array('en','it');

// -----------------------------------------------------------------------------

/*
    Log settings.
*/
$this->namespace('LOG', true);

/*
    If you would like to enable logging set this variable to boolean true.
*/
$this['ACTIVE'] = false;

/*
    The threshold to choose what gets logged.
    The loggers use the syslog priorities as levels.
    @see http://php.net/syslog
*/
$this['THRESHOLD'] = LOG_DEBUG;

/*
    The date format string for log entries.
*/
$this['DATE_FORMAT'] = 'Y-m-d H:i:s';

/*
    If you would like to enable logging set this variable to boolean true.
*/
$this['TYPE'] = COMMENT_LOGGER;

/*
    [DATEFILE_LOGGER]
    The real path to the logging directory.
*/
$this['PATH'] = ROOT . 'logs/';

/*
    [FILE_LOGGER]
    The real path to the log file.
*/
$this['FILE'] = ROOT . 'log.txt';

/*
    [EMAIL_LOGGER]
*/
$this['MAIL_THRESHOLD'] = 0;
$this['MAILTO'] = 'logicoder@gmail.com';
$this['HEADERS'] = 'From: info@marcodeltongo.com';

/*
    [COMMENT_LOGGER]
*/
$this['BUFFERED'] = true;

// -----------------------------------------------------------------------------

/*
    Router settings.
*/
$this->namespace('ROUTER', true);

/*
    The name of the file(s) containing the routes.
*/
$this['FILE'] = 'urls' . EXT;

/*
    Use query string to build request URI.
*/
$this['QUERY_STRING'] = true;

// -----------------------------------------------------------------------------

/*
    HTTP Request settings.
*/
$this->namespace('REQUEST', true);

/*
    Apply sanitization automatically to all data.
*/
$this['XSS_FILTERING'] = true;

/*
    Apply sanitization only when value is accessed.
*/
$this['LAZY_FILTERING'] = true;

// -----------------------------------------------------------------------------

/*
    HTTP Response settings.
*/
$this->namespace('RESPONSE', true);

/*
    Compress output.
*/
$this['COMPRESS_OUTPUT'] = true;

<?php
/**
 * Logicoder Web Application Framework - Main settings
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/configuration.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/*
    This is the main settings file. Here you must tell Logicoder where to find
    things, how to run and things like that.

    The defaults try to autodetect as much as possible but, for speed sake,
    you should change them accordingly to your live server once deployed.

    Take a look at http://www.logicoder.com/documentation/configuration.html to
    see which settings are available and how they change behaviours.
*/

// -----------------------------------------------------------------------------

/**
 * Secret Key.
 *
 * It's YOUR secret key, change it !
 */
define('SECRET_KEY', 'abracadabra');

/**
 * Development or public ?
 *
 * This setting is useful to manage different settings between development and
 * deployment servers like database connections or directories location.
 */
define('DEVELOPMENT', true); #($_SERVER['HTTP_HOST'] == 'localhost'));

/**
 * Run in debug mode or live mode ?
 *
 * NOTE: Don't use debug mode on a publicly available server, EVER !
 */
define('DEBUG', true);

// -----------------------------------------------------------------------------
/**
 * The project name.
 */
define('PROJECT_NAME', 'A Logicoder Project');

/**
 * Absolute path to the base/root directory.
 */
define('ROOT', realpath('.') . '/');

/**
 * Absolute path to the directory that holds the Logicoder framework.
 */
define('LOGICODER_ROOT', ROOT . 'logicoder/');

/**
 * Absolute path to the write enabled directory for logs, cache, uploads...
 */
define('DATA_ROOT', ROOT . 'data/');

/**
 * Applications folder.
 */
define('APPS_ROOT', ROOT . 'apps/');

/**
 * Absolute path to the directory that holds the project public files.
 */
define('PUBLIC_ROOT', ROOT);

/**
 * The public path to the project index file.
 */
define('PROJECT_URL', 'http://' . $_SERVER['HTTP_HOST'] . str_replace('index' . EXT, '', $_SERVER['SCRIPT_NAME']));

/**
 * The index file, leave blank if mod_rewrite is used.
 */
define('PROJECT_INDEX', basename(__FILE__));

/**
 * Absolute path to the directory that holds media for this project.
 */
define('MEDIA_ROOT', PUBLIC_ROOT . 'media/');

/**
 * The base URL that handles the media served from MEDIA_ROOT.
 */
define('MEDIA_URL', PROJECT_URL . 'media/');

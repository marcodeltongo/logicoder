<?php if (!isset($this)) { header("HTTP/1.0 404 Not Found"); die(); }
/**
 * Logicoder Web Application Framework - Project database settings
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/configuration.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/*
    Database settings.
*/
$this->namespace('DB', true);

/*
    Should we auto-open a connection ?
*/
$this['AUTOCONNECT'] = false;

/*
    Driver to use / Database Type.

    See documentation for available drivers.
*/
$this['DRIVER'] = 'PHP_MySQLi';

/*
    The DB server hostname or path to the database.
*/
$this['HOSTNAME'] = 'localhost';

/*
    The schema/database name.
*/
$this['DATABASE'] = 'logicoder';

/*
    The username to use to access the server.
*/
$this['USERNAME'] = 'root';

/*
    The password to use to access the server.
*/
$this['PASSWORD'] = '';

/*
    The table prefix to automatically add to the table names.
*/
$this['TABLE_PREFIX'] = '';

/*
    The charset to use to connect to db, create schemas and return results.
*/
$this['CHARSET'] = 'utf8';

/*
    Wheter to use a persistent connection or not.
*/
$this['PERSISTENT'] = false;

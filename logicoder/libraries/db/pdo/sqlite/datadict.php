<?php
/**
 * Logicoder Web Application Framework - Database library components
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * DDL Schema Builder class for PDO SQLite driver.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PDO_SQLite_DataDict extends Logicoder_DB_PDO_DataDict
{
    // -------------------------------------------------------------------------
    //  Overridden properties.
    // -------------------------------------------------------------------------

    /**
     * DB types attributes.
     */
    public $aDBAttrs    = array (   'NULL'              => ' NULL',
                                    '!NULL'             => ' NOT NULL',
                                    'DEFAULT'           => ' DEFAULT ',
                                    'AUTOINC'           => '');

    /**
     * DB index types.
     */
    public $aDBIndex    = array (   'PRIMARY'           => 'PRIMARY KEY (`%s`)');

    // -------------------------------------------------------------------------
    //  Overloaded methods.
    // -------------------------------------------------------------------------
}
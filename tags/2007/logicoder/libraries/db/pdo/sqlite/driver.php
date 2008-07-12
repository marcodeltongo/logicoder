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
 * PDO SQLite Database Driver library.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PDO_SQLite_Driver extends Logicoder_DB_PDO_Driver
{
    // -------------------------------------------------------------------------
    //  Overridden properties.
    // -------------------------------------------------------------------------

    /**
     * The datadict builder class.
     */
    protected $sDataDictClass   = 'Logicoder_DB_PDO_SQLite_DataDict';

    // -------------------------------------------------------------------------
    //  Overloaded methods.
    // -------------------------------------------------------------------------

    /**
     * Builds a DSN.
     *
     * @param   array   $aC         Connection parameters
     *
     * @return  string  The built DSN string
     */
    protected function __dsn ( array $aC )
    {
        return str_replace('pdo_', '', strtolower($aC['DRIVER'])) . ':' . $aC['HOSTNAME'] . $aC['DATABASE'];
    }
}
// END Logicoder_DB_PDO_SQLite_Driver class

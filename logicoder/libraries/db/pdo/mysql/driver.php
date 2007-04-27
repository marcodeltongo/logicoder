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
 * PDO MySQL Database Driver library.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PDO_MySQL_Driver extends Logicoder_DB_Driver
{
    // -------------------------------------------------------------------------
    //  OVERLOADED METHODS
    // -------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * If required opens a connection to the database.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     */
    public function __construct ( array $aConnection, array $aOptions = null )
    {
        /*
            Let the parent do the work.
        */
        parent::__construct($aConnection, $aOptions);

        if (defined('DB_CHARSET'))
        {
            /*
                Set CHARSET to use for everything.
            */
            $this->execute('SET NAMES ' . DB_CHARSET);
        }
    }
}
// END Logicoder_DB_PDO_MySQL_Driver class

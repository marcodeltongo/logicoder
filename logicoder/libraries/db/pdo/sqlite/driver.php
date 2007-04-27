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
    //  OVERLOADED METHODS
    // -------------------------------------------------------------------------

    /**
     * Open a connection to the database.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     */
    function connect ( array $aConnect, array $aOptions = null )
    {
        if (is_null($this->oDB))
        {
            try
            {
                $aOptions = (array)$aOptions;
                /*
                    Persistent or volatile connection ?
                */
                if (isset($aConnect['PERSISTENT']))
                {
                    $aOptions[PDO::ATTR_PERSISTENT] = $aConnect['PERSISTENT'];
                }
                /*
                    Set error reporting mode.
                */
                if (isset($aConnect['DEBUG']) and $aConnect['DEBUG'] === true)
                {
                    $aOptions[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
                }
                /*
                    Prepare DSN for SQLite 3.
                */
                $sDSN = str_replace('pdo_', '', strtolower($aConnect['DRIVER'])) .
                                                ':' . $aConnect['HOSTNAME'] .
                                                $aConnect['DATABASE'];
                /*
                    Open connection.

                    TODO: Check for the "The auto-commit mode cannot be changed for this driver" bug.
                */
                $this->oDB = new PDO($sDSN, $aConnect['USERNAME'], $aConnect['PASSWORD'], (array)$aOptions);
            }
            catch (PDOException $oException)
            {
                return user_error('Database connection failed: ' . $oException->getMessage(), E_USER_ERROR);
            }
        }
    }
}
// END Logicoder_DB_PDO_SQLite_Driver class

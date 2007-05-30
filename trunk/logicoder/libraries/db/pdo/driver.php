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
 * PDO Database Driver library.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PDO_Driver extends Logicoder_DB_Driver
{
    // -------------------------------------------------------------------------
    //  Overridden properties.
    // -------------------------------------------------------------------------

    /**
     * The statement class.
     */
    protected $sStmtClass       = 'Logicoder_DB_PDO_Statement';

    /**
     * The datadict builder class.
     */
    protected $sDataDictClass   = 'Logicoder_DB_PDO_DataDict';

    // -------------------------------------------------------------------------
    //  Overloaded methods.
    // -------------------------------------------------------------------------

    /**
     * Adds quotes to passed value.
     *
     * @param   string  $mVal   The value to quote
     *
     * @return string   The passed value quoted
     */
    public function quote ( $mVal )
    {
        return (is_string($mVal)) ? $this->oDB->quote($mVal) : $mVal;
    }

    // -------------------------------------------------------------------------
    //  Abstract methods.
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
        return str_replace('pdo_', '', strtolower($aC['DRIVER'])) .
                                        ':host=' . $aC['HOSTNAME'] .
                                        ';dbname=' . $aC['DATABASE'];
    }

    /**
     * Open a connection to the database.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     */
    public function connect ( array $aConnect, array $aOptions = null )
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
                    Open connection.
                */
                $this->oDB = new PDO($this->__dsn($aConnect),
                                     $aConnect['USERNAME'],
                                     $aConnect['PASSWORD'],
                                     (array)$aOptions);
            }
            catch (PDOException $oException)
            {
                return user_error('Database connection failed: ' . $oException->getMessage(), E_USER_ERROR);
            }
        }
    }

    /**
     * Close connection.
     */
    public function disconnect ( /* void */ )
    {
        if (!is_null($this->oDB))
        {
            $this->oDB = null;
        }
    }

    /**
     * Prepare a new query.
     *
     * @param   mixed   $mSQL       SQL string or object to prepare
     *
     * @return  object  Return a new Statement
     */
    public function prepare ( $mSQL )
    {
        /*
            Get a query string.
        */
        $mSQL = $this->_prepare($mSQL);
        /*
            Return a DB statement instance or throw an exception.
        */
        if ($oStmt = $this->oDB->prepare($mSQL))
        {
            return new $this->sStmtClass($oStmt);
        }
        else
        {
            $aError = $this->oDB->errorInfo();
            throw new Logicoder_DB_Exception($aError[2], $aError[1]);
        }
    }

    /**
     * Returns the auto generated id used in the last query.
     *
     * @return  mixed   Returns the auto generated id used in the last query
     */
    public function inserted_id ( /* void */ )
    {
        return $this->oDB->lastInsertId();
    }

    // -------------------------------------------------------------------------
    //  Transaction support methods.
    // -------------------------------------------------------------------------

    /**
     * Begins a new transaction and turns auto-commit off.
     *
     * @return  mixed   True on success or false on failure
     */
    public function begin ( /* void */ )
    {
        return $this->bTransaction = $this->oDB->beginTransaction();
    }

    /**
     * Commits a transaction and turns auto-commit back on.
     *
     * @return  mixed   True on success or false on failure
     */
    public function commit ( /* void */ )
    {
        if ($this->oDB->commit())
        {
            $this->bTransaction = false;
            return true;
        }
        return false;
    }

    /**
     * Rolls back a transaction and turns auto-commit back on.
     *
     * @return  mixed   True on success or false on failure
     */
    public function rollback ( /* void */ )
    {
        if ($this->oDB->rollBack())
        {
            $this->bTransaction = false;
            return true;
        }
        return false;
    }
}
// END Logicoder_DB_PDO_Driver class

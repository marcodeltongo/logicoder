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
 * MySQL Database Driver using PHP MySQL Improved library.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PHP_MySQLi_Driver extends Logicoder_DB_Driver
{
    /**
     * Named placeholders substitions.
     */
    protected $aNamed = array();

    // -------------------------------------------------------------------------
    //  Overridable properties.
    // -------------------------------------------------------------------------

    /**
     * The statement class.
     */
    protected $sStmtClass = 'Logicoder_DB_PHP_MySQLi_Statement';

    // -------------------------------------------------------------------------
    //  Overridable methods.
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
        /*
            Set CHARSET to use for everything.
        */
        $this->execute('SET NAMES ' . DB_CHARSET);
    }

    /**
     * Open a connection to the database.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     */
    public function connect ( array $aConnection, array $aOptions = null )
    {
        if (is_null($this->oDB))
        {
            $aC = new Logicoder_OverArray($aConnection);
            /*
                Open connection.
            */
            $this->oDB = new MySQLi($aConnection['HOSTNAME'],
                                    $aConnection['USERNAME'],
                                    $aConnection['PASSWORD'],
                                    $aConnection['DATABASE']);
            /*
                Check for errors.
            */
            if ($this->oDB === false)
            {
                throw new Logicoder_DB_Exception('Database connection failed: ' . mysqli_connect_error());
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
            $this->oDB->close();
            $this->oDB = null;
        }
    }

    /**
     * Overloaded _prepare to add named placeholders emulation.
     *
     * @param   mixed   $mSQL       SQL string or object to prepare
     *
     * @return  string  Return eventually modified sql query string
     */
    public function _prepare ( $mSQL )
    {
        /*
            Call parent _prepare to start out with an SQL string.
        */
        $mSQL = parent::_prepare($mSQL);
        /*
            Search all named placeholders.
        */
        preg_match_all('/(:\w+)/', $mSQL, $aMatches = array());
        preg_replace('/:\w+/', '?', $mSQL);
        $this->aNamed[$mSQL] = array_fill_keys($aMatches, null);
        /*
            Return eventually modified sql query string.
        */
        return $mSQL;
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
            Prepare underlying statement object.
        */
        $oStmt = $this->oDB->prepare($mSQL);
        /*
            Return a DB statement instance.
        */
        return new $this->sStmtClass($oStmt, $this->aNamed[$mSQL]);
    }

    /**
     * Returns the auto generated id used in the last query.
     *
     * @return  mixed   Returns the auto generated id used in the last query
     */
    public function inserted_id ( /* void */ )
    {
        return $this->oDB->insert_id;
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
        return $this->oDB->autocommit(false);
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
            $this->oDB->autocommit(true);
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
        if ($this->oDB->rollback())
        {
            $this->oDB->autocommit(true);
            return true;
        }
        return false;
    }
}
// END Logicoder_DB_PHP_MySQL_Driver class

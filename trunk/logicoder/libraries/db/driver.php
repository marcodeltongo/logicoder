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
 * Abstract Database Driver class.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_DB_Driver
{
    /**
     * The database object.
     */
    protected $oDB          = null;

    /**
     * The last query used.
     */
    protected $sLastQuery   = null;

    /**
     * The run queries.
     */
    protected $aQueries     = array();

    /**
     * Whether we have an open transaction.
     */
    protected $bTransaction = false;

    // -------------------------------------------------------------------------
    //  Overridable properties.
    // -------------------------------------------------------------------------

    /**
     * The statement class.
     */
    protected $sStmtClass       = 'Logicoder_DB_Statement';

    /**
     * The query builder class.
     */
    protected $sQueryClass      = 'Logicoder_DB_Query';

    /**
     * The datadict builder class.
     */
    protected $sDataDictClass   = 'Logicoder_DB_DataDict';

    // -------------------------------------------------------------------------
    //  Main methods.
    // -------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * If required opens a connection to the database.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     */
    public function __construct ( array $aConnect, array $aOptions = null )
    {
        /*
            Connect.
        */
        if (is_null($this->oDB) and !empty($aConnect))
        {
            $this->connect($aConnect, $aOptions);
        }
    }

    /**
     * Destructor.
     */
    public function __destruct ( /* void */ )
    {
        /*
            Close any open transaction.
        */
        if ($this->bTransaction)
        {
            $this->rollback();
        }
        /*
            Close connection.
        */
        $this->disconnect();
    }

    /**
     * Prepare a new query SQL.
     *
     * @param   mixed   $mSQL       SQL string or object to prepare
     *
     * @return  string  Return eventually modified sql query string
     */
    public function _prepare ( $mSQL )
    {
        /*
            Sanity checks.
        */
        if (!is_string($mSQL) and !is_object($mSQL))
        {
            throw new Logicoder_DB_Exception('Must pass a valid SQL query.');
        }
        elseif (is_string($mSQL))
        {
            if ($mSQL == '')
            {
                throw new Logicoder_DB_Exception('Empty SQL query.');
            }
        }
        elseif ($mSQL instanceof $this->sQueryClass
                or $mSQL instanceof $this->sDataDictClass)
        {
            /*
                Get the source from object.
            */
            $mSQL = $mSQL->sql();
        }
        else
        {
            throw new Logicoder_DB_Exception('Unknown query object type.');
        }
        /*
            Save copies of the query.
        */
        $this->sLastQuery = $mSQL;
        if (!isset($this->aQueries[$mSQL]))
        {
            $this->aQueries[$mSQL] = 0;
        }
        /*
            Return eventually modified sql query string.
        */
        return $mSQL;
    }

    /**
     * Query the database and return all results.
     *
     * @param   mixed   $mSQL       SQL query string or object to run
     * @param   array   $aData      Data for bound vars
     *
     * @return  mixed   Return statement or false
     */
    public function query ( $mSQL, array $aData = null )
    {
        /*
            Prepare query.
        */
        $oStmt = $this->prepare($mSQL);
        /*
            Update stats.
        */
        ++$this->aQueries[$this->sLastQuery];
        /*
            Return statement or false.
        */
        return ($oStmt->execute($aData)) ? $oStmt : false;
    }

    /**
     * Query the database and return only the first row as associative array.
     *
     * @param   mixed   $mSQL       SQL query string or object to run
     * @param   array   $aData      Data for bound vars
     *
     * @return  mixed   Return all results or false
     */
    public function query_all ( $mSQL, array $aData = null )
    {
        /*
            Run query.
        */
        $oStmt = $this->query($mSQL, $aData);
        /*
            Return all results or false.
        */
        return ($oStmt !== false) ? $oStmt->results() : false;
    }

    /**
     * Query the database and return only the first row as associative array.
     *
     * @param   mixed   $mSQL       SQL query string or object to run
     * @param   array   $aData      Data for bound vars
     *
     * @return  mixed   Return first row of results or false
     */
    public function query_row ( $mSQL, array $aData = null )
    {
        /*
            Run query.
        */
        $oStmt = $this->query($mSQL, $aData);
        /*
            Return first row or false.
        */
        return ($oStmt !== false) ? $oStmt->row() : false;
    }

    /**
     * Query the database and return only a column.
     *
     * @param   mixed   $mSQL       SQL query string or object to run
     * @param   array   $aData      Data for bound vars
     * @param   integer $nColumn    Column index to return
     *
     * @return  mixed   Return nColumn of first row or false
     */
    public function query_col ( $mSQL, array $aData = null, $nColumn = 0 )
    {
        /*
            Run query.
        */
        $oStmt = $this->query($mSQL, $aData);
        /*
            Return column or false.
        */
        return ($oStmt !== false) ? $oStmt->col($nColumn) : false;
    }

    /**
     * Execute a query without results.
     *
     * @param   mixed   $mSQL       SQL query string or object to run
     * @param   array   $aData      Data for bound vars
     *
     * @return  mixed   True on success or false on failure
     */
    public function execute ( $mSQL, array $aData = null )
    {
        return ($this->query($mSQL, $aData) !== false);
    }

    // -------------------------------------------------------------------------
    //  Abstract methods.
    // -------------------------------------------------------------------------

    /**
     * Open a connection to the database.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     */
    abstract function connect ( array $aConnect, array $aOptions = null );

    /**
     * Close connection.
     */
    abstract function disconnect ( /* void */ );

    /**
     * Prepare a new query.
     *
     * @param   mixed   $mSQL       SQL string or object to prepare
     *
     * @return  object  Return a new Statement
     */
    abstract function prepare ( $mSQL );

    /**
     * Returns the auto generated id used in the last query.
     *
     * @return  mixed   Returns the auto generated id used in the last query
     */
    abstract public function inserted_id ( /* void */ );

    // -------------------------------------------------------------------------
    //  Transaction support methods.
    // -------------------------------------------------------------------------

    /**
     * Begins a new transaction and turns auto-commit off.
     *
     * @return  mixed   True on success or false on failure
     */
    abstract public function begin ( /* void */ );

    /**
     * Commits a transaction and turns auto-commit back on.
     *
     * @return  mixed   True on success or false on failure
     */
    abstract public function commit ( /* void */ );

    /**
     * Rolls back a transaction and turns auto-commit back on.
     *
     * @return  mixed   True on success or false on failure
     */
    abstract public function rollback ( /* void */ );

    // -------------------------------------------------------------------------
    //  Useful shortcut methods.
    // -------------------------------------------------------------------------

    /**
     * Prepare a SELECT * query and return all results.
     *
     * @param   string  $sTable     Table name
     * @param   integer $nLimit     Limit clause
     * @param   integer $nOffset    Offset clause
     *
     * @return  mixed   Return all results or false
     */
    public function get ( $sTable, $nLimit = null, $nOffset = null )
    {
        /*
            Prepare query.
        */
        $oQ = new $this->sQueryClass();
        $oQ->select()->from($sTable)->limit($nLimit, $nOffset);
        /*
            Return results.
        */
        return $this->query_all($oQ);
    }

    /**
     * Prepare a SELECT * query with where condition and return all results.
     *
     * @param   string  $sTable     Table name
     * @param   array   $aWhere     Where conditions array
     * @param   integer $nLimit     Limit clause
     * @param   integer $nOffset    Offset clause
     *
     * @return  mixed   Return all results or false
     */
    public function get_where ( $sTable, $aWhere = null, $nLimit = null, $nOffset = null )
    {
        /*
            Prepare query.
        */
        $oQ = new $this->sQueryClass();
        $oQ->select()->from($sTable)->where($aWhere)->limit($nLimit, $nOffset);
        /*
            Return results.
        */
        return $this->query_all($oQ);
    }

    /**
     * Prepare a SELECT * query with where and order condition and return all results.
     *
     * @param   string  $sTable     Table name
     * @param   array   $aWhere     Where conditions array
     * @param   mixed   $mOrder     String, comma separated list or array
     * @param   integer $nLimit     Limit clause
     * @param   integer $nOffset    Offset clause
     *
     * @return  mixed   Return all results or false
     */
    public function get_where_order ( $sTable, $aWhere = null, $mOrder = null, $nLimit = null, $nOffset = null )
    {
        /*
            Prepare query.
        */
        $oQ = new $this->sQueryClass();
        $oQ->select()->from($sTable)->where($aWhere)->order($mOrder)->limit($nLimit, $nOffset);
        /*
            Return results.
        */
        return $this->query_all($oQ);
    }

    /**
     * Prepare a SELECT COUNT(*) query with where condition and return count.
     *
     * @param   string  $sTable     Table name
     * @param   array   $aWhere     Where conditions array
     * @param   mixed   $mFields    String, comma separated list or array
     *
     * @return  mixed   Return count or false
     */
    public function get_count ( $sTable, $aWhere = null, $mFields = array('*') )
    {
        /*
            Prepare query.
        */
        $oQ = new $this->sQueryClass();
        $oQ->select()->count($mWhere)->from($sTable)->where($aWhere);
        /*
            Return results.
        */
        return $this->query_col($oQ);
    }

    /**
     * Return last query.
     */
    public function last_query ( /* void */ )
    {
        return $this->sLastQuery;
    }

    /**
     * Return all run queries.
     */
    public function queries ( /* void */ )
    {
        return $this->aQueries;
    }

    // -------------------------------------------------------------------------
    //  Builders methods.
    // -------------------------------------------------------------------------

    /**
     * Return a SQL builder instance.
     *
     * @param   string  $sDriver    Specific SQL builder version
     *
     * @return  object  Return a SQL builder instance
     */
    public function sql_builder ( $sDriver = false )
    {
        if ($sDriver === false)
        {
            /*
                Driver type same as this.
            */
            return new $this->sQueryClass();
        }
        else
        {
            /*
                Force driver type.
            */
            $sDriver = "Logicoder_DB_{$sDriver}_Query";
            return new $sDriver();
        }
    }

    /**
     * Return a DDL builder instance.
     *
     * @param   string  $sDriver    Specific DDL builder version
     *
     * @return  object  Return a DDL builder instance
     */
    public function ddl_builder ( $sDriver = false )
    {
        if ($sDriver === false)
        {
            /*
                Driver type same as this.
            */
            return new $this->sDataDictClass();
        }
        else
        {
            /*
                Force driver type.
            */
            $sDriver = "Logicoder_DB_{$sDriver}_DataDict";
            return new $sDriver();
        }
    }
}
// END Logicoder_DB_Driver class

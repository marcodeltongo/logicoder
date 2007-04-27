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
 * Abstract Database Statement class.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_DB_Statement implements IteratorAggregate
{
    /**
     * Underlying statement.
     */
    protected $oStmt;

    /**
     * Underlying DB object.
     */
    protected $oDB;

    /**
     * Conversion table.
     */
    protected $aResultModes = array( DB_FETCH_ASSOC  => DB_FETCH_ASSOC,
                                     DB_FETCH_NUM    => DB_FETCH_NUM,
                                     DB_FETCH_OBJ    => DB_FETCH_OBJ );

    // -------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @param   object  $oStmt      Underlying statement object
     */
    public function __construct ( $oStmt )
    {
        /*
            Check paramenters.
        */
        if (is_null($oStmt))
        {
            throw new Logicoder_DB_Exception('Wrong statement constructor parameters.');
        }
        /*
            Save references.
        */
        $this->oStmt = $oStmt;
    }

    /**
     * IteratorAggregate
     */
    public function getIterator ( /* void */ )
    {
        return new ArrayIterator($this->results());
    }

    // -------------------------------------------------------------------------
    //  ABSTRACT METHODS
    // -------------------------------------------------------------------------

    /**
     * Binds a value to a parameter in the prepared statement
     *
     * @param   array   $aData      Data for bound values
     */
    abstract public function bind ( array $aData = null );

    /**
     * Executes the prepared query.
     *
     * @param   array   $aData      Data for bound values
     *
     * @return  boolean True for success or false for failure
     */
    abstract public function execute ( array $aData = null );

    /**
     * Get results.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   Results as per fetching mode or false
     */
    abstract public function results ( $iMode = DB_FETCH_ASSOC );

    /**
     * Get next row of results.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   First row of results as per fetching mode or false
     */
    abstract public function row ( $iMode = DB_FETCH_ASSOC );

    /**
     * Returns the number of rows used by the last operation.
     *
     * @return  numeric Returns the number of rows used by the last operation.
     */
    abstract public function num_rows ( /* void */ );

    /**
     * Returns the number of fields in the record.
     *
     * @return  numeric Returns the number of fields in the record.
     */
    abstract public function num_fields ( /* void */ );

    /**
     * Free results.
     *
     * @param   boolean $bFreeStmt  Whether to close the underlying statement
     */
    abstract public function free_results ( $bFreeStmt = false );

    // -------------------------------------------------------------------------
    //  PUBLIC METHODS
    // -------------------------------------------------------------------------

    /**
     * Get results as a numerically indexed array.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   Results as a numerically indexed array or false
     */
    public function results_num ( /* void */ )
    {
        return $this->results(DB_FETCH_NUM);
    }

    /**
     * Get results as an array of objects.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   Results as an array of objects or false
     */
    public function results_obj ( /* void */ )
    {
        return $this->results(DB_FETCH_OBJ);
    }

    /**
     * Get a single row of results as a numerically indexed array.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   First row of results as a numerically indexed array or false
     */
    public function row_num ( /* void */ )
    {
        return $this->row(DB_FETCH_NUM);
    }

    /**
     * Get a single row of results as an array of objects.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   First row of results as object or false
     */
    public function row_obj ( /* void */ )
    {
        return $this->row(DB_FETCH_OBJ);
    }

    /**
     * Get a single column of results.
     *
     * @param   mixed   $mColumn    Numeric or string column index
     *
     * @return  mixed   Value on success or null on failure
     */
    public function col ( $mColumn = 0 )
    {
        /*
            Check if column is numeric or suppose string.
        */
        if (is_numeric($mColumn))
        {
            $aRow = $this->row_num();
        }
        else
        {
            $aRow = $this->row();
        }
        /*
            Return result.
        */
        return $aRow[$mColumn];
    }

    // -------------------------------------------------------------------------

    /**
     * Destructor.
     */
    public function __destruct ( /* void */ )
    {
        $this->free_results();
    }
}
// END Logicoder_DB_Statement class

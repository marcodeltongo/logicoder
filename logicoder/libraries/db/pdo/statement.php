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
class Logicoder_DB_PDO_Statement extends Logicoder_DB_Driver
{
    /**
     * Overloaded conversion table.
     */
    protected $aResultModes = array( DB_FETCH_ASSOC  => PDO::FETCH_ASSOC,
                                     DB_FETCH_NUM    => PDO::FETCH_NUM,
                                     DB_FETCH_OBJ    => PDO::FETCH_OBJ);

    // -------------------------------------------------------------------------
    //  OVERLOADED METHODS
    // -------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @param   object  $oStmt      Underlying statement object
     * @param   array   $aNamed     Named placeholders mappings
     */
    public function __construct ( PDOStatement $oStmt )
    {
        parent::__construct($oStmt);
    }

    // -------------------------------------------------------------------------
    //  IMPLEMENTED ABSTRACT METHODS
    // -------------------------------------------------------------------------

    /**
     * Binds a value to a parameter in the prepared statement
     *
     * @param   array   $aData      Data for bound values
     *
     * @return  boolean True on success or false on failure
     */
    public function bind ( array $aData = null )
    {
        if (is_null($aData))
        {
            return false;
        }
        foreach ($aData as $k => $v)
        {
            $k = (is_string($k)) ? ':' . $k : (int)$k + 1;
            if ($this->oStmt->bindValue($k, $v) === false)
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Executes the prepared query.
     *
     * @param   array   $aData      Data for bound values
     *
     * @return  boolean True for success or false for failure
     */
    public function execute ( array $aData = null )
    {
        $this->oStmt->closeCursor();
        return $this->oStmt->execute((array)$aData);
    }

    /**
     * Get results.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   Results as per fetching mode or false
     */
    public function results ( $iMode = DB_FETCH_ASSOC )
    {
        /*
            Return results.
        */
        return $this->oStmt->fetchAll($this->aResultModes[$iMode]);
    }

    /**
     * Get next row of results.
     *
     * @param   integer $iMode      Results fetching mode
     *
     * @return  mixed   First row of results as per fetching mode or false
     */
    public function row ( $iMode = DB_FETCH_ASSOC )
    {
        /*
            Return result.
        */
        return $this->oStmt->fetch($this->aResultModes[$iMode]);
    }

    /**
     * Returns the number of rows used by the last operation.
     *
     * @return  numeric Returns the number of rows used by the last operation.
     */
    public function num_rows ( /* void */ )
    {
        if (substr_compare($this->oStmt->queryString, 'select', 0, 6, true) == 0)
        {
            return count($this->results());
        }
        else
        {
            return $this->oStmt->rowCount();
        }
    }

    /**
     * Returns the number of fields in the record.
     *
     * @return  numeric Returns the number of fields in the record.
     */
    public function num_fields ( /* void */ )
    {
        return $this->oStmt->columnCount();
    }

    /**
     * Free results.
     *
     * @param   boolean $bFreeStmt  Whether to close the underlying statement
     */
    public function free_results ( $bFreeStmt = false )
    {
        if (!is_null($this->oStmt))
        {
            $this->oStmt->closeCursor();
            if ($bFreeStmt)
            {
                $this->oStmt = null;
            }
        }
    }
}
// END Logicoder_DB_PDO_Statement class

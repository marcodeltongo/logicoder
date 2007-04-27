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
 * MySQL Database Statement using PHP MySQL Improved library.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PHP_MySQLi_Statement extends Logicoder_DB_Statement
{
    /**
     * Named placeholders substitions.
     */
    protected $aNamed;

    /**
     * Statement results metadata.
     */
    protected $oMeta;

    /**
     * Statement results fields metadata.
     */
    protected $aFieldsMeta;

    // -------------------------------------------------------------------------
    //  OVERLOADED METHODS
    // -------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @param   object  $oStmt      Underlying statement object
     * @param   array   $aNamed     Named placeholders mappings
     */
    public function __construct ( mysqli_stmt $oStmt, array $aNamed = array() )
    {
        parent::__construct($oStmt);
        $this->aNamed = $aNamed;
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
        if (is_assoc($aData))
        {
            /*
                It's an associative array, must emulate named placeholders.
            */
            $aSorted = $this->aNamed;
            foreach ($aSorted as $k => $v)
            {
                if (isset($aData[$k]))
                {
                    $aSorted[$k] = $aData[$k];
                }
            }
        }
        /*
            Get types...
        */
        $sTypes = '';
        foreach ($aData as $value)
        {
            /*
                Prepare type string.
            */
            switch (get_type($value))
            {
                case 'integer':
                case 'boolean':
                    $sTypes .= 'i';
                break;
                case 'float':
                    $sTypes .= 'd';
                break;
                case 'string':
                    $sTypes .= 's';
                break;
            }
        }
        array_unshift($aData, $sTypes);
        /*
            Call underlying method and return.
        */
        return call_user_func_array(array($this->oStmt, "bind_param"), $aData);
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
        $this->bind($aData);
        $this->oMeta = null;
        $bRet = $this->oStmt->execute();
        if ($this->oMeta = $this->oStmt->result_metadata())
        {
            $this->aFieldsMeta = $this->oMeta->fetch_fields();
            $this->oStmt->store_result();
        }
        return $bRet;
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
        $aResults = array();
        while ($result = $this->row($iMode))
        {
            $aResults[] = $result;
        }
        return $aResults;
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
            Prepare as per fetching mode.
        */
        switch ($iMode)
        {
            case DB_FETCH_ASSOC:
                /*
                    Prepare an associative array.
                */
                $mFields = array();
                foreach ($this->aFieldsMeta as $field)
                {
                    $mFields[$field->name] = null;
                }
            break;
            case DB_FETCH_NUM:
                /*
                    Prepare an indexed array.
                */
                $mFields = array_fill(0, $this->oMeta->field_count, null);
            break;
            case DB_FETCH_OBJ:
                /*
                    Prepare an object.
                */
                $mFields = new object();
                foreach ($this->oMeta->fetch_fields() as $field)
                {
                    $name = $field->name;
                    $mFields->$name = null;
                }
            break;
        }
        /*
            Bind results.
        */
        call_user_func_array(array($this->oStmt, "bind_result"), $mFields);
        /*
            Fetch data and return.
        */
        if ($this->oStmt->fetch() === true)
        {
            return $mFields;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the number of rows used by the last operation.
     *
     * @return  numeric Returns the number of rows used by the last operation.
     */
    public function num_rows ( /* void */ )
    {
        if (!is_null($this->oMeta))
        {
            return $this->oStmt->num_rows;
        }
        else
        {
            return $this->oStmt->affected_rows;
        }
    }

    /**
     * Returns the number of fields in the record.
     *
     * @return  numeric Returns the number of fields in the record.
     */
    public function num_fields ( /* void */ )
    {
        return $this->oMeta->field_count;
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
            $this->oStmt->free_result();
            if ($bFreeStmt)
            {
                $this->oStmt->close();
                $this->oStmt = null;
            }
        }
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
// END Logicoder_DB_PHP_MySQL_Statement class

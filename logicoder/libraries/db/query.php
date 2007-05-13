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
 * ANSI SQL92 Query Builder class.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_Query
{
    /**
     * Query method/type.
     */
    protected $sMethod;

    /**
     * Query fields.
     */
    protected $aFields;

    /**
     * Insert|Update values.
     */
    protected $aValues;

    /**
     * Query select distinct.
     */
    protected $bDistinct;

    /**
     * Query tables.
     */
    protected $aTables;

    /**
     * Query join types.
     */
    protected $aJoinTypes = array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER');

    /**
     * Query join.
     */
    protected $aJoin;

    /**
     * Query operator regex.
     */
    protected $rOperator = '/(\s|<|>|!|=|is null|is not null)/i';

    /**
     * Query where condition.
     */
    protected $aWhere;

    /**
     * Query like condition.
     */
    protected $aLike;

    /**
     * Query having condition.
     */
    protected $aHaving;

    /**
     * Query group by condition.
     */
    protected $aGroupBy;

    /**
     * Query order by condition.
     */
    protected $aOrderBy;

    /**
     * Query limit.
     */
    protected $nLimit;

    /**
     * Query offset.
     */
    protected $nOffset;

    // -------------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct ( /* void */ )
    {
        $this->clean();
    }

    /**
     * toString conversion.
     */
    public function __toString ( /* void */ )
    {
        return $this->sql();
    }

    // -------------------------------------------------------------------------
    //  SQL GENERATION METHODS
    // -------------------------------------------------------------------------

    /**
     * Compile and return SQL query.
     */
    public function sql ( /* void */ )
    {
        /*
            No method, nothing to return.
        */
        if ($this->sMethod === false)
        {
            return null;
        }
        /*
            Switch by method.
        */
        $func = '_sql_' . $this->sMethod;
        return $this->$func();
    }

    /**
     * Compile and return a SELECT query.
     */
    protected function _sql_select ( /* void */ )
    {
        /*
            Start SQL query.
        */
        $sql = ($this->bDistinct) ? 'SELECT DISTINCT ' : 'SELECT ';
        /*
            Select which fields ?
        */
        $sql .= (empty($this->aFields)) ? '*' : implode(', ', $this->aFields);
        /*
            From which tables ?
        */
        $sql .= (empty($this->aTables)) ? '' : "\nFROM " . implode(', ', $this->aTables);
        /*
            Join something ?
        */
        if (!empty($this->aJoin))
        {
            foreach ($this->aJoin as $j)
            {
                $sql .= "\n{$j[0]} JOIN {$j[1]} ON {$j[2]}";
            }
        }
        /*
            Where ?
        */
        if (!empty($this->aWhere))
        {
            $sql .= "\nWHERE ";
            foreach ($this->aWhere as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
            }
            if (!empty($this->aLike))
            {
                $sql .= "\nAND ";
                foreach ($this->aLike as $i)
                {
                    $sql .= "\n{$i[0]}{$i[1]} LIKE '%{$i[2]}%'";
                }
            }
        }
        elseif (!empty($this->aLike))
        {
            $sql .= "\nWHERE ";
            foreach ($this->aLike as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} LIKE '%{$i[2]}%'";
            }
        }
        /*
            Group by ?
        */
        if (!empty($this->aGroupBy))
        {
            $sql .= "\nGROUP BY " . implode(', ', $this->aGroupBy);
        }
        /*
            Having ?
        */
        if (!empty($this->aHaving))
        {
            $sql .= "\nHAVING ";
            foreach ($this->aHaving as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
            }
        }
        /*
            Order by ?
        */
        if (!empty($this->aOrderBy))
        {
            $sql .= "\nORDER BY ";
            foreach ($this->aOrderBy as $i)
            {
                $sql .= "{$i[0]} {$i[1]}, ";
            }
            $sql = trim($sql, ', ');
        }
        /*
            Query limit ?
        */
        if ($this->nLimit !== false)
        {
            if ($this->nOffset !== false)
            {
                $sql .= "\nLIMIT " . $this->nOffset . ', ' . $this->nLimit;
            }
            else
            {
                $sql .= "\nLIMIT 0, " . $this->nLimit;
            }
        }
        /*
            Return SQL.
        */
        return $sql;
    }

    /**
     * Compile and return an INSERT query.
     */
    protected function _sql_insert ( /* void */ )
    {
        /*
            Start SQL query.
        */
        $sql = 'INSERT INTO ';
        /*
            In which table ?
        */
        $sql .= $this->aTables[0] . ' ';
        /*
            Use fields names ?
        */
        $sql .= (empty($this->aFields)) ? '' : "\n(" . implode(', ', $this->aFields) . ')';
        /*
            Insert values.
        */
        $sql .= "\nVALUES (" . implode(", ", $this->aValues) . ")";
        /*
            Return SQL.
        */
        return $sql;
    }

    /**
     * Compile and return an UPDATE query.
     */
    protected function _sql_update ( /* void */ )
    {
        /*
            Start SQL query.
        */
        $sql = 'UPDATE ';
        /*
            Which table ?
        */
        $sql .= $this->aTables[0] . "\nSET ";
        /*
            Fields and values.
        */
        foreach ($this->aFields as $k => $f)
        {
            $sql .= $f . ' = ' . $this->aValues[$k] . ', ';
        }
        $sql = rtrim($sql, ', ');
        /*
            Where ?
        */
        if (!empty($this->aWhere))
        {
            $sql .= "\nWHERE ";
            foreach ($this->aWhere as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
            }
            if (!empty($this->aLike))
            {
                $sql .= "\nAND ";
                foreach ($this->aLike as $i)
                {
                    $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
                }
            }
        }
        elseif (!empty($this->aLike))
        {
            $sql .= "\nWHERE ";
            foreach ($this->aLike as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
            }
        }
        /*
            Return SQL.
        */
        return $sql;
    }

    /**
     * Compile and return a DELETE query.
     */
    protected function _sql_delete ( /* void */ )
    {
        /*
            Start SQL query.
        */
        $sql = 'DELETE FROM ';
        /*
            Which table ?
        */
        $sql .= $this->aTables[0];
        /*
            Where ?
        */
        if (!empty($this->aWhere))
        {
            $sql .= "\nWHERE ";
            foreach ($this->aWhere as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
            }
            if (!empty($this->aLike))
            {
                $sql .= "\nAND ";
                foreach ($this->aLike as $i)
                {
                    $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
                }
            }
        }
        elseif (!empty($this->aLike))
        {
            $sql .= "\nWHERE ";
            foreach ($this->aLike as $i)
            {
                $sql .= "\n{$i[0]}{$i[1]} {$i[2]}";
            }
        }
        /*
            Return SQL.
        */
        return $sql;
    }

    /**
     * Clean string parts.
     */
    public function clean ( /* void */ )
    {
        $this->sMethod  = false;
        $this->aFields  = array();
        $this->aValues  = array();
        $this->aTables  = array();
        $this->aJoin    = array();
        $this->aWhere   = array();
        $this->aLike    = array();
        $this->aHaving  = array();
        $this->aGroupBy = array();
        $this->aOrderBy = array();
        $this->nLimit   = false;
        $this->nOffset  = false;
    }

    // -------------------------------------------------------------------------

    /**
     * Select fields to query.
     */
    public function select ( $mFields = array('*'), $sTable = null )
    {
        $this->clean();
        /*
            Set query method/type.
        */
        $this->sMethod = 'SELECT';
        /*
            Convert from comma separated list to array.
        */
        if (!is_array($mFields))
        {
            $mFields = explode(',', str_replace(', ', ',', $mFields));
        }
        /*
            Save.
        */
        $this->aFields = $mFields;
        /*
            Save table.
        */
        if (!is_null($sTable))
        {
            $this->aTables = array(trim($sTable));
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Select fields to query.
     */
    public function count ( $mFields = array('*'), $sTable = null )
    {
        /*
            Adjust paramaters.
        */
        if (is_array($mFields))
        {
            $mFields = implode(', ', $mFields);
        }
        $mFields = array('count(' . $mFields . ')');
        /*
            Use select method.
        */
        $this->select($mFields, $sTable);
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set distinct selection.
     */
    public function distinct ( $bDistinct = true )
    {
        /*
            Save.
        */
        $this->bDistinct = (bool)$bDistinct;
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set tables to query from.
     */
    public function from ( $mTables )
    {
        /*
            Convert from comma separated list to array.
        */
        if (!is_array($mTables))
        {
            $mTables = explode(',', str_replace(', ', ',', $mTables));
        }
        /*
            Save.
        */
        $this->aTables = array_merge($this->aTables, $mTables);
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set tables to join.
     */
    public function join ( $sTable, $sCondition, $sType = '' )
    {
        if ($sType != '')
        {
            $sType = strtoupper(trim($sType));
            $sType = (in_array($sType, $this->aJoinTypes)) ? $sType . ' ' : '';
        }
        /*
            Save.
        */
        $this->aJoin = array_merge($this->aJoin, array($sType, $sTable, $sCondition));
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set where conditions.
     */
    public function where ( $mFields, $mValue = null, $sMethod = 'AND' )
    {
        /*
            Convert single field to array.
        */
        if (!is_array($mFields))
        {
            $mFields = array($mFields => $mValue);
        }
        /*
            Write where condition for each field.
        */
        foreach ($mFields as $k => $v)
        {
            /*
                Add operator if missing.
            */
            if (!preg_match($this->rOperator, $k))
            {
                $k .= ' =';
            }
            /*
                Save line.
            */
            if (empty($this->aWhere))
            {
                $this->aWhere[] = array('', $k, $v);
            }
            else
            {
                $this->aWhere[] = array($sMethod . ' ', $k, $v);
            }
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set where condition joint by logical AND
     */
    public function and_where ( $mFields, $mValue = null )
    {
        /*
            Return for method chaining.
        */
        return $this->where($mFields, $mValue, 'AND');
    }

    /**
     * Set where condition joint by logical OR
     */
    public function or_where ( $mFields, $mValue = null )
    {
        /*
            Return for method chaining.
        */
        return $this->where($mFields, $mValue, 'OR');
    }

    /**
     * Set like condition.
     */
    public function like ( $mFields, $mMatch = null, $sMethod = 'AND' )
    {
        /*
            Convert single field to array.
        */
        if (!is_array($mFields))
        {
            $mFields = array($mFields => $mMatch);
        }
        /*
            Write like condition for each field.
        */
        foreach ($mFields as $k => $v)
        {
            if (empty($this->aLike))
            {
                $this->aLike[] = array('', $k, $v);
            }
            else
            {
                $this->aLike[] = array($sMethod . ' ', $k, $v);
            }
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set like condition.
     */
    public function and_like ( $mFields, $mMatch = null )
    {
        /*
            Return for method chaining.
        */
        return $this->like($mFields, $mMatch, 'AND');
    }

    /**
     * Set like condition.
     */
    public function or_like ( $mFields, $mMatch = null )
    {
        /*
            Return for method chaining.
        */
        return $this->like($mFields, $mMatch, 'OR');
    }

    /**
     * Set having condition.
     */
    public function having ( $mFields, $mMatch = null, $sMethod = 'AND' )
    {
        /*
            Convert single field to array.
        */
        if (!is_array($mFields))
        {
            $mFields = array($mFields => $mMatch);
        }
        /*
            Write having condition for each field.
        */
        foreach ($mFields as $k => $v)
        {
            if (empty($this->aHaving))
            {
                $this->aHaving[] = array('', $k, $v);
            }
            else
            {
                $this->aHaving[] = array($sMethod . ' ', $k, $v);
            }
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set having condition.
     */
    public function and_having ( $mFields, $mMatch = null )
    {
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set having condition.
     */
    public function or_having ( $mFields, $mMatch = null )
    {
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set group by condition.
     */
    public function group_by ( $mFields )
    {
        /*
            Convert from comma separated list to array.
        */
        if (!is_array($mFields))
        {
            $mFields = explode(',', str_replace(', ', ',', $mFields));
        }
        /*
            Save.
        */
        $this->aGroupBy = array_merge($this->aGroupBy, $mFields);
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set order by condition.
     */
    public function order ( $mFields, $sMode = 'ASC' )
    {
        /*
            Convert from comma separated list to array.
        */
        if (!is_array($mFields))
        {
            $mFields = explode(',', str_replace(', ', ',', $mFields));
        }
        /*
            Save.
        */
        foreach ($mFields as $v)
        {
            $sMode = (strcasecmp($sMode, 'DESC')) ? strtoupper($sMode) : 'ASC';
            $this->aOrderBy[] = array($v, $sMode);
        }
        /*
            Return for method chaining.
        */
        return $this;
    }
    /**
     * order() alias.
     */
    public function order_by ( $mFields, $sMode = 'ASC' )
    {
        return $this->order($mFields, $sMode);
    }

    /**
     * Set query limit and offset.
     */
    public function limit ( $nLimit, $nOffset = false )
    {
        if (is_null($nLimit))
        {
            return $this;
        }
        /*
            Save limit.
        */
        $this->nLimit = (int)$nLimit;
        /*
            Save offset.
        */
        if ($nOffset !== false)
        {
            $this->nOffset = (int)$nOffset;
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Set query offset.
     */
    public function offset ( $nOffset )
    {
        if (is_null($nOffset))
        {
            return $this;
        }
        /*
            Save offset.
        */
        $this->nOffset = (int)$nOffset;
        /*
            Return for method chaining.
        */
        return $this;
    }

    // -------------------------------------------------------------------------

    /**
     * Insert query.
     */
    public function insert ( array $aFields, $sTable = null )
    {
        $this->clean();
        /*
            Set query method/type.
        */
        $this->sMethod = 'INSERT';
        /*
            Check for an associative array (uses array helper).
        */
        if (is_assoc($aFields))
        {
            $this->aFields = array_keys($aFields);
            $this->aValues = array_values($aFields);
        }
        else
        {
            $this->aValues = $aFields;
        }
        /*
            Save table if passed.
        */
        if (!is_null($sTable))
        {
            $this->aTables[] = trim($sTable);
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Insert data into table.
     */
    public function into ( $sTable )
    {
        $this->aTables = array(trim($sTable));
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Update table.
     */
    public function update ( $sTable, array $aFields = null, array $aValues = null )
    {
        $this->clean();
        /*
            Set query method/type.
        */
        $this->sMethod = 'UPDATE';
        /*
            Save table.
        */
        $this->aTables = array(trim($sTable));
        /*
            Check for an associative array (uses array helper).
        */
        if (!is_null($aFields))
        {
            $this->set($aFields, $aValues);
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Update table with values.
     */
    public function set ( array $aFields, array $aValues = null )
    {
        /*
            Check for an associative array (uses array helper).
        */
        if (is_assoc($aFields) and is_null($aValues))
        {
            $this->aFields = array_keys($aFields);
            $this->aValues = array_values($aFields);
        }
        else
        {
            $this->aFields = $aFields;
            $this->aValues = $aValues;
        }
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Delete records.
     */
    public function delete ( $sTable = null, array $aFields = null )
    {
        $this->clean();
        /*
            Set query method/type.
        */
        $this->sMethod = 'DELETE';
        /*
            Save table.
        */
        if (!is_null($sTable))
        {
            $this->aTables = array(trim($sTable));
        }
        /*
            Check for an associative array (uses array helper).
        */
        if (!is_null($aFields))
        {
            $this->where($aFields);
        }
        /*
            Return for method chaining.
        */
        return $this;
    }
}
// END Logicoder_DB_Query class

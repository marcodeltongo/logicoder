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
 * ANSI SQL92 DDL Schema Builder class.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_DataDict
{
    // -------------------------------------------------------------------------
    //  Overridable properties.
    // -------------------------------------------------------------------------

    /**
     * Meta types to DB types table.
     */
    public $aMetaTypes  = array (   'C'  => 'VARCHAR($maxlength)',
                                    'I'  => 'INTEGER',
                                    'U'  => 'INTEGER UNSIGNED',
                                    'UZ' => 'INTEGER UNSIGNED ZEROFILL',
                                    'F'  => 'NUMERIC($digits, $decimals)',
                                    'B'  => 'BOOL',
                                    'X'  => 'TEXT',
                                    'D'  => 'DATE',
                                    'T'  => 'TIME',
                                    'DT' => 'DATETIME' );

    /**
     * DB types to meta types table.
     */
    public $aDBTypes    = false;

    /**
     * DB types attributes.
     */
    public $aDBAttrs    = array (   'NULL'              => ' NULL',
                                    '!NULL'             => ' NOT NULL',
                                    'DEFAULT'           => ' DEFAULT ',
                                    'AUTOINC'           => ' AUTO_INCREMENT');

    /**
     * DB index types.
     */
    public $aDBIndex    = array (   'INDEX'             => 'KEY `%s` (`%s`)',
                                    'UNIQUE'            => 'UNIQUE KEY `%s` (`%s`)',
                                    'PRIMARY'           => 'PRIMARY KEY (`%s`)');

    /**
     * Database operations syntax.
     */
    public $sCreateDatabase         = 'CREATE DATABASE IF NOT EXISTS `%s`';
    public $sCreateDatabaseCharset  = 'CREATE DATABASE IF NOT EXISTS `%s` DEFAULT CHARACTER SET %s';
    public $sDropDatabase           = 'DROP DATABASE IF EXISTS `%s`';
    public $sUseDatabase            = 'USE `%s`';

    /**
     * Table operations syntax.
     */
    public $sCreateTable    = 'CREATE TABLE IF NOT EXISTS `%s`';
    public $sAlterTable     = 'ALTER TABLE `%s`';
    public $sRenameTable    = 'RENAME TABLE `%s` TO `%s`';
    public $sDropTable      = 'DROP TABLE IF EXISTS `%s`';

    /**
     * Column operations syntax (along with sAlterTable).
     */
    public $sAddColumn      = 'ADD `%s`';
    public $sAlterColumn    = 'CHANGE `%s` `%s`';
    public $sRenameColumn   = 'CHANGE `%s` `%s`';
    public $sDropColumn     = 'DROP `%s`';

    /**
     * Index operations syntax.
     */
    public $sCreateIndex    = 'ADD INDEX (`%s`)';
    public $sCreateUnique   = 'ADD UNIQUE (`%s`)';
    public $sCreatePrimary  = 'ADD PRIMARY KEY (`%s`)';
    public $sDropIndex      = 'DROP INDEX `%s`';

    // -------------------------------------------------------------------------
    //  Public methods.
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
        return (is_string($mVal)) ? '"' . addslashes($mVal) . '"' : $mVal;
    }

    /**
     * Return the passed name quoted.
     */
    public function quote_name ( $sName )
    {
        /*
            If already quoted, return it unchanged or quote it.
        */
        if ( preg_match('/^`(.+)`$/', $sName) ) {
            return $sName;
        }
        return '`' . $sName . '`';
    }

    /**
     * Return the passed name without quotes.
     */
    public function unquote_name ( $sName )
    {
        if ( preg_match('/`(.+)`/', $sName, $aMatches) ) {
            return $aMatches[1];
        }
        return $sName;
    }

    /**
     * Create a DB.
     */
    public function create_database ( $sDbName, $sCharset = false )
    {
        if ($sCharset !== false)
        {
            return sprintf($this->sCreateDatabaseCharset, $sDbName, $sCharset);
        }
        else
        {
            return sprintf($this->sCreateDatabase, $sDbName);
        }
    }

    /**
     * Drop a DB.
     */
    public function drop_database ( $sDbName )
    {
        return sprintf($this->sDropDatabase, $sDbName);
    }

    /**
     * Set current DB.
     */
    public function use_database ( $sDbName )
    {
        return sprintf($this->sUseDatabase, $sDbName);
    }

    /**
     * Builds field with options from array.
     *
     * @param   array   $aField     Field information
     *
     * @return  string  DDL for the field
     */
    protected function __build_field ( array &$aField )
    {
        /*
            Get name.
        */
        $sName = $this->quote_name($aField['db_column']);
        /*
            Get the metatype.
        */
        $sType = $this->aMetaTypes[$aField['db_type']];
        /*
            Replace the params, if needed.
        */
        if (strpos($sType, '$') !== false)
        {
            preg_match_all('/\$(\w*)/', $sType, $aMatches);

            foreach ($aMatches[1] as $option)
            {
                $sType = str_replace('$'.$option, $aField[$option], $sType);
            }
        }
        /*
            Now the attributes, first is NULL or NOT NULL
        */
        $sAttr = (isset($aField['null']) and !$aField['null']) ? $this->aDBAttrs['!NULL'] : $this->aDBAttrs['NULL'];
        /*
            There's a default value ? If needed, escape it.
        */
        if (isset($aField['default']) and !is_null($aField['default']))
        {
            $sAttr .= $this->aDBAttrs['DEFAULT'];
            $def = $aField['default'];
            $sAttr .=  (is_string($def)) ? '"'.addslashes($def).'"' : $def;
        }
        /*
            Auto-increment ?
        */
        $sAttr .= (isset($aField['auto_inc']) and $aField['auto_inc']) ? $this->aDBAttrs['AUTOINC'] : '';
        /*
            Append line.
        */
        return $sName . ' ' . $sType . $sAttr;
    }

    /**
     * Create a table.
     */
    public function create_table ( $sTableName, array $aFields = null )
    {
        $sql = sprintf($this->sCreateTable, $sTableName) . ' (';
        $idx = '';
        /*
            Loop thru fields.
        */
        foreach ($aFields as $field)
        {
            /*
                Create and append line.
            */
            $sql .= "\n\t" . $this->__build_field($field) . ',';
            /*
                Should be indexed ?
            */
            if (isset($field['primary_key']) and $field['primary_key'] and isset($this->aDBIndex['PRIMARY']))
            {
                $idx .= "\n\t" . sprintf($this->aDBIndex['PRIMARY'], $field['db_column']) . ',';
            }
            elseif (isset($field['unique']) and $field['unique'] and isset($this->aDBIndex['UNIQUE']))
            {
                $idx .= "\n\t" . sprintf($this->aDBIndex['UNIQUE'], $field['db_column'], $field['db_column']) . ',';
            }
            elseif (isset($field['index']) and $field['index'] and isset($this->aDBIndex['INDEX']))
            {
                $idx .= "\n\t" . sprintf($this->aDBIndex['INDEX'], $field['db_column'], $field['db_column']) . ',';
            }
        }
        return rtrim($sql . $idx, ',') . "\n)";
    }

    /**
     * Alter a table.
     */
    public function alter_table ( $sTableName, array $aFields = null )
    {
        throw new Exception('NOT IMPLEMENTED');
    }

    /**
     * Rename a table.
     */
    public function rename_table ( $sOldName, $sNewName )
    {
        return sprintf($this->sRenameTable, $sOldName, $sNewName);
    }

    /**
     * Drop a table.
     */
    public function drop_table ( $sTableName )
    {
        return sprintf($this->sDropTable, $sTableName);
    }

    /**
     * Create a simple index.
     */
    public function create_index ( $sIndexName, $sTableName, array $aFields, $sPattern = false )
    {
        $sPattern = ($sPattern === false) ? $this->sCreateIndex : $sPattern;
        /*
            Alter table.
        */
        $sql = sprintf($this->sAlterTable, $sTableName) . "\n";
        /*
            Create index.
        */
        $sql .= sprintf($sPattern, $sIndexName) . " (";
        /*
            Save last.
        */
        foreach ($aFields as $field)
        {
            /*
                Indexed field.
            */
            $sql .= "\n" . $this->quote_name($field) . ",";
        }
        /*
            Return SQL.
        */
        return rtrim($sql, ',') . "\n)";
    }

    /**
     * Create a unique index.
     */
    public function create_unique_index ( $sIndexName, $sTableName, array $aFields )
    {
        /*
            Return SQL generated by method.
        */
        return $this->create_index($sIndexName, $sTableName, $aFields, $this->sCreateUnique);
    }

    /**
     * Create a primary index.
     */
    public function create_primary_index ( $sIndexName, $sTableName, array $aFields )
    {
        /*
            Return SQL generated by method.
        */
        return $this->create_index($sIndexName, $sTableName, $aFields, $this->sCreatePrimary);
    }

    /**
     * Drop an index.
     */
    public function drop_index ( $sIndexName, $sTableName )
    {
        /*
            Alter table.
        */
        $sql = sprintf($this->sAlterTable, $sTableName) . "\n";
        /*
            Drop index.
        */
        $sql .= sprintf($this->sDropIndex, $sIndexName);
        /*
            Return SQL.
        */
        return $sql;
    }

    /**
     * Add a column.
     */
    public function add_column ( $sTableName, array $aFields )
    {
        throw new Exception('NOT IMPLEMENTED');
    }

    /**
     * Alter a column.
     */
    public function alter_column ( $sTableName, array $aFields )
    {
        throw new Exception('NOT IMPLEMENTED');
    }

    /**
     * Rename a column.
     */
    public function rename_column ( $sTableName, $sOldName, $sNewName )
    {
        /*
            Alter table.
        */
        $sql = sprintf($this->sAlterTable, $sTableName) . "\n";
        /*
            Rename column.
        */
        $sql .= sprintf($this->sRenameColumn, $sOldName, $sNewName);
        /*
            Return SQL.
        */
        return $sql;
    }

    /**
     * Drop a column.
     */
    public function drop_column ( $sTableName, array $aFields )
    {
        /*
            Alter table.
        */
        $sql = sprintf($this->sAlterTable, $sTableName) . "\n";
        /*
            Save last.
        */
        $last = array_pop($aFields);
        foreach ($aFields as $field)
        {
            /*
                Drop column.
            */
            $sql .= sprintf($this->sDropColumn, $field) . ",\n";
        }
        /*
            Last column.
        */
        $sql .= sprintf($this->sDropColumn, $last) . ")";
        /*
            Return SQL.
        */
        return $sql;
    }
}
// END Logicoder_DB_DataDict class

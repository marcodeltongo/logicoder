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
    public $aMetaTypes  = array(
                        'C'  => 'VARCHAR­($maxlength)',
                        'I'  => 'INTEGER',
                        'U'  => 'INTEGER UNSIGNED',
                        'F'  => 'NUMERIC($digits, $decimals)',
                        'B'  => 'BOOL',
                        'X'  => 'TEXT',
                        'D'  => 'DATE',
                        'T'  => 'TIME',
                        'DT' => 'DATETIME');

    /**
     * DB types to meta types table.
     */
    public $aDBTypes    = false;

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
     * Return the DB type for the passed meta type.
     */
    public function meta_to_db_type ( $sMetaType )
    {
        /*
            Return type if valid or null.
        */
        return (isset($this->aMetaTypes[$sFieldType])) ? $this->aMetaTypes[$sFieldType] : null;
    }

    /**
     * Return the meta type for the passed db type.
     */
    public function db_to_meta_type ( $sDBType )
    {
        /*
            Lazy building of the array.
        */
        if ($this->aDBTypes === false)
        {
            $this->aDBTypes = array_flip($this->aMetaTypes);
        }
        /*
            Return type if valid or null.
        */
        return (isset($this->aMetaTypes[$sFieldType])) ? $this->aMetaTypes[$sFieldType] : null;
    }

    /**
     * Return the passed name quoted.
     */
    public function quote_name ( $sName )
    {
        /*
            If already quoted, return it unchanged or quote it.
        */
        if ( preg_match('/^`(.+)`$/', $sName, $aMatches = array()) ) {
            return $sName;
        }
        return '`' . $sName . '`';
    }

    /**
     * Return the passed name without quotes.
     */
    public function unquote_name ( $sName )
    {
        if ( preg_match('/^`(.+)`$/', $sName, $aMatches = array()) ) {
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
     * Create a table.
     */
    public function create_table ( $sTableName, array $aFields = null )
    {
        $sql = sprintf($this->sCreateTable, $sTableName) . ' (';
        /*
            Loop thru fields.
        */
        foreach ($aFields as $field)
        {
            if (is_array($field) and isset($this->aMetaTypes[$field[1]]))
            {
                /*
                    Get the name.
                */
                $line = $this->quote_name(array_shift($f));
                /*
                    Get the metatype.
                */
                $meta = $this->aMetaTypes[array_shift($f)];
                /*
                    Replace the params, if needed.
                */
                if (strpos($meta, '$') !== false)
                {
                    foreach ($field as $k => $v)
                    {
                        str_replace('$'.$k, $v, $meta);
                    }
                }
                /*
                    Append line.
                */
                $field = $name . ' ' . $meta;
            }
            /*
                Append line.
            */
            $sql .= "\n\t" . $field;
        }
        return $sql . "\n)";
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
        $sql .= sprintf($sPattern, $sIndexName) . " (\n";
        /*
            Save last.
        */
        $last = array_pop($aFields);
        foreach ($aFields as $field)
        {
            /*
                Indexed field.
            */
            $sql .= $this->quote_name($field) . ",\n";
        }
        /*
            Last field.
        */
        $sql .= $this->quote_name($last) . ")";
        /*
            Return SQL.
        */
        return $sql;
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

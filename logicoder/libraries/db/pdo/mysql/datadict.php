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
 * DDL Schema Builder class for PDO MySQL driver.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PDO_MySQL_DataDict extends Logicoder_DB_PDO_DataDict
{
    // -------------------------------------------------------------------------
    //  Overloaded methods.
    // -------------------------------------------------------------------------

    /**
     * Create a table.
     */
    public function create_table ( $sTableName, array $aFields = null )
    {
        $_charset = (defined('DB_CHARSET')) ? DB_CHARSET : 'utf8';
        return parent::create_table($sTableName, $aFields) . ' ENGINE=InnoDB DEFAULT CHARSET=' . $_charset .';';
    }
}

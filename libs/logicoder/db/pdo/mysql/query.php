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
class Logicoder_DB_PDO_MySQL_Query extends Logicoder_DB_Query
{

    /**
     * Adds a case-sensitive comparison filter to query.
     *
     * @param   string  $sField     Field name
     * @param   mixed   $mValue     Value for filter or placeholder
     *
     * @return  string  SQL for the WHERE clause
     */
    public function __is ( $sField, $mValue )
    {
        return "BINARY $sField = $mValue";
    }

    /**
     * Adds a case-sensitive string matching filter to query.
     *
     * @param   string  $sField     Field name
     * @param   mixed   $mValue     Value for filter or placeholder
     *
     * @return  string  SQL for the WHERE clause
     */
    public function __contains ( $sField, $mValue )
    {
        return "BINARY $sField LIKE $mValue";
    }

}
// END Logicoder_DB_Query class

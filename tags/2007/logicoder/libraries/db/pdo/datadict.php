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
 * ANSI SQL92 DDL Schema Builder class for PDO drivers.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_PDO_DataDict extends Logicoder_DB_DataDict
{
    // -------------------------------------------------------------------------
    //  Overloaded methods.
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
        return (is_string($mVal)) ? PDO::quote($mVal) : $mVal;
    }
}
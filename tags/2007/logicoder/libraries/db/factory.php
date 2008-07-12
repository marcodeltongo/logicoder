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
 * Database Wrapper Factory class.
 *
 * @package     Logicoder
 * @subpackage  Database
 * @link        http://www.logicoder.com/documentation/database.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_Factory implements Logicoder_iFactory
{
    /**
     * Returns a new instance of the class.
     *
     * @param   array   $aConnect   Connection parameters
     * @param   array   $aOptions   Connection options
     *
     * @return  object  A driver instance
     */
    static function instance ( array $aConnect = null, array $aOptions = null )
    {
        if (is_null($aConnect))
        {
            if (is_null($aConnect = Logicoder::instance()->settings->get_ns('DB')))
            {
                throw new Logicoder_DB_Exception('Invalid connection parameters.');
            }
        }
        /*
            Prepare driver name.
        */
        $sClass = 'DB_' . $aConnect['DRIVER'] . '_Driver';
        /*
            Load and return instance.
        */
        $sClass = Logicoder::instance()->load->library($sClass);
        return new $sClass($aConnect, $aOptions);
    }
}
// END Logicoder_DB_Factory class

<?php
/**
 * Logicoder Web Application Framework - Loggers factory
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Logger factory class.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_Factory implements Logicoder_iFactory
{
    /**
     * Private Constructor, don't call it.
     */
    private function __construct ( /* void */ ) { /* void */ }

    /**
     * Factory method.
     *
     * @param   string  $sLogType   The required logger
     *
     * @return  object  Returns a new instance
     */
    public static function instance ( $sLogType = LOG_TYPE )
    {
        /*
            Return dummy logger if logging not required.
        */
        if (LOG_ACTIVE === false)
        {
            return new Logicoder_Logger_Dummy();
        }
        /*
            Prepare parameters.
        */
        $aParams = array_slice(func_get_args(), 1);
        $aParams = empty($aParams) ? null : $aParams;
        /*
            Select the required class.
        */
        $sClass = 'Logicoder_Logger_' . $sLogType;
        /*
            Return a new instance.
        */
        return (empty($aParams)) ? new $sClass() : new $sClass($aParams);
    }
}
// END Logicoder_Logger_Factory class

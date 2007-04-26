<?php
/**
 * Logicoder Web Application Framework - Cache container factory
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Cache container factory class.
 *
 * @package     Logicoder
 * @subpackage  Cache
 * @link        http://www.logicoder.com/documentation/cache.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Cache_Factory implements Logicoder_iFactory
{
    /**
     * Returns a new instance of a Cache container class.
     *
     * @param   string  $sType      Cache container type
     *
     * @return  object  Returns a cache instance
     */
    public static function instance ( $sType, array $aOptions = null )
    {
        $sClass = 'Logicoder_Cache_' . $sType;
        return new $sClass($aOptions);
    }
}
// END Logicoder_Cache_Factory class

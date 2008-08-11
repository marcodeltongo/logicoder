<?php
/**
 * Logicoder Web Application Framework - Controller library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Controller class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/controllers.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Controller
{
    /**
     * Local registry reference.
     */
    protected $oRegistry = null;

    /**
     * Constructor
     *
     * @param   object  $oRegistry  A Logicoder_Registry instance
     */
    public function __construct ( Logicoder_Registry $oRegistry = null )
    {
        if (!is_null($oRegistry))
        {
            $this->oRegistry = $oRegistry;
        }
        else
        {
            $this->oRegistry = Logicoder::instance();
        }
    }

    /**
     * Overload magic property getter function.
     *
     * @param   string  $sKey   The name/key string
     *
     * @return  mixed   The key value
     */
    protected function __get ( $sKey )
    {
        return (isset($this->$sKey)) ? $this->$sKey : $this->oRegistry->get($sKey);
    }
}
// END Logicoder_Controller class

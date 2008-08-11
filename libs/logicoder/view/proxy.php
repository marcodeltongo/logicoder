<?php
/**
 * Logicoder Web Application Framework - Simple Proxy View
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Proxy View class.
 *
 * This is basically a proxy view.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View_Proxy extends Logicoder_View_Abstract
{
    /**
     * Method to proxy for class views.
     */
    protected $sMethod;

    /**
     * Set/get view class method.
     *
     * @param   string  $sMethod    The method to call or null to get it
     *
     * @return  mixed   Method name if null passed or itself for chaining
     */
    public function method ( $sMethod = null )
    {
        if (!is_null($sMethod))
        {
            if (!method_exists($this, $sMethod))
            {
                throw new Logicoder_404("Proxy_View method $sMethod unknown.");
            }
            /*
                Save passed method.
            */
            $this->sMethod = $sMethod;
            /*
                Return myself for method chaining.
            */
            return $this;
        }
        /*
            Return method if called with null parameter.
        */
        return $this->sMethod;
    }

    /**
     * Simply call local class method.
     */
    public function _parse ( /* void */ )
    {
        ob_start();
        $bOK = call_user_func(array($this, $this->sMethod));
        $this->sParsed = ob_get_clean();
        return ($bOK !== false);
    }
}
// END Logicoder_View_Proxy class

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
 * Simple View class.
 *
 * This is basically a proxy view.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View extends Logicoder_View_Abstract
{
    /**
     * Simply call local class method.
     */
    public function _parse ( /* void */ )
    {
        ob_start();
        call_user_func(array($this, $this->sMethod));
        $this->sParsed = ob_get_clean();
    }
}
// END Logicoder_View class

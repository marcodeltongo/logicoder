<?php
/**
 * Logicoder Web Application Framework - Template View
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Templated HTML View class.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View_Template extends Logicoder_View_Abstract
{
    /**
     * Parse source template.
     */
    public function _parse ( /* void */ )
    {
        ob_start();
        /*
            TO DO !
        */
        $this->sParsed = ob_get_clean();
        return false;
    }
}
// END Logicoder_View_Template class

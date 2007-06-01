<?php
/**
 * Logicoder Web Application Framework - HTML View
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * HTML View class.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View_HTML extends Logicoder_View_Abstract
{
    /**
     * Parse (include) view HTML source.
     */
    public function _parse ( /* void */ )
    {
        /*
            Include the HTML view file.
        */
        ob_start();
        $bOK = include $this->sFilename;
        $this->sParsed = ob_get_clean();
        return $bOK;
    }
}
// END Logicoder_View_HTML class

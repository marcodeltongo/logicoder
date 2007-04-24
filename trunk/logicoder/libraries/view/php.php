<?php
/**
 * Logicoder Web Application Framework - PHP View
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * PHP View class.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View_PHP extends Logicoder_View_Abstract
{
    /**
     * Extract data and include view PHP code.
     */
    public function _parse ( /* void */ )
    {
        /*
            Simply extracts data as local references.
        */
        extract($this->aData, EXTR_REFS);
        /*
            Include the PHP view file.
        */
        ob_start();
        include $this->sFilename;
        $this->sParsed = ob_get_clean();
    }
}
// END Logicoder_View_PHP class

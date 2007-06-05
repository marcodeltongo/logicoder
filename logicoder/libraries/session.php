<?php
/**
 * Logicoder Web Application Framework - Session library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Session class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/sessions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Session
{
    /**
     * Request instance.
     */
    protected $oRequest;

    /**
     * Constructor
     */
    public function __construct ( Logicoder_Request $oRequest = null )
    {
    }
}
// END Logicoder_Session class

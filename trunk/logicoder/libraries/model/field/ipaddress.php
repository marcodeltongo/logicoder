<?php
/**
 * Logicoder Web Application Framework - Models library components
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * IP Address Field
 *
 * An IP address, in string format (i.e. "24.124.1.30").
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_IPAddress extends Logicoder_Model_Field_Char
{
    /**
     * Override default constructor.
     */
    public function __construct ( $sField, array $aOptions = array() )
    {
        /*
            Override default values, if not set in model definition.
        */
        if (!isset($aOptions['maxlength']))
        {
            $aOptions['maxlength'] = 15;
        }
        /*
            Call parent constructor.
        */
        parent::__construct($sField, $aOptions);
    }
}
// END Logicoder_Model_Field_IPAddress class

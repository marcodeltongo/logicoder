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
 * Null Boolean Field
 *
 * Like a BooleanField, but allows NULL as one of the options.
 * HTML input: select
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_NullBoolean extends Logicoder_Model_Field_Boolean
{
    /**
     * Override default constructor.
     */
    public function __construct ( $sField, array $aOptions = array() )
    {
        /*
            Force default options.
        */
        $aOptions['null'] = true;
        /*
            Override default values, if not set in model definition.
        */
        if (!isset($aOptions['choices']))
        {
            $aOptions['choices'] = array('N/D','Yes','No');
        }
        /*
            Call parent constructor.
        */
        parent::__construct($sField, $aOptions);
    }
}
// END Logicoder_Model_Field_NullBoolean class

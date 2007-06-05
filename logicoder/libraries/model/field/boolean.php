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
 * Boolean Field
 *
 * A true/false field.
 * HTML input: checkbox
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Boolean extends Logicoder_Model_Field_Abstract
{
    /**
     * Define column type.
     */
    public $db_type = 'B';

    /**
     * Override default constructor.
     */
    public function __construct ( $sField, array $aOptions = array() )
    {
        /*
            Override default values, if not set in model definition.
        */
        if (!isset($aOptions['null']))
        {
            $aOptions['null'] = false;
        }
        if (!isset($aOptions['choices']))
        {
            $aOptions['choices'] = array('Yes','No');
        }
        /*
            Call parent constructor.
        */
        parent::__construct($sField, $aOptions);
    }

    /**
     *  Sanitizes a value for this field.
     *
     *  @param  mixed   $mValue     Value to sanitize
     *
     *  @return boolean Sanitized value
     */
    public function sanitize ( $mValue )
    {
        return sanitize_boolean($mValue);
    }
}
// END Logicoder_Model_Field_Boolean class

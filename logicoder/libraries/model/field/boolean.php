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
class Logicoder_Model_Field_Boolean extends Logicoder_Model_Field
{
    /**
     * Define column type.
     */
    public $db_type = 'B';

    /**
     * Override default constructor.
     */
    public function __construct ( $oModel, $sField, array $aOptions = array() )
    {
        /*
            Override default values.
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
        parent::__construct($oModel, $sField, $aOptions);
    }
}
// END Logicoder_Model_Field_Boolean class

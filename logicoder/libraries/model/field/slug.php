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
 * Slug Field
 *
 * Slugified string.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Slug extends Logicoder_Model_Field_Char
{
    /**
     * Get default value from other fields.
     */
    public $prepopulate_from = false;

    /**
     * Override default constructor.
     */
    public function __construct ( $sField, array $aOptions = array() )
    {
        /*
            Override default values, if not set in model definition.
        */
        if (!isset($aOptions['index']))
        {
            $aOptions['index'] = true;
        }
        /*
            Call parent constructor.
        */
        parent::__construct($sField, $aOptions);
    }
}
// END Logicoder_Model_Field_Slug class

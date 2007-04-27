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
 * Auto-incremental Field
 *
 * An Integer field that automatically increments.
 * HTML input: hidden
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Auto extends Logicoder_Model_Field_Unsigned
{
    /**
     * Override default constructor.
     */
    public function __construct ( $oModel, $sField, array $aOptions = array() )
    {
        /*
            Override default values.
        */
        $aOptions['null'] = false;
        $aOptions['blank'] = false;
        $aOptions['auto_inc'] = true;
        $aOptions['unique'] = true;
        $aOptions['editable'] = false;
        /*
            Call parent constructor.
        */
        parent::__construct($oModel, $sField, $aOptions);
    }
}
// END Logicoder_Model_Field_Auto class

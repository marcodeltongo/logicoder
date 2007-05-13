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
 * Float Field
 *
 * A floating-point number.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Float extends Logicoder_Model_Field_Abstract
{
    /**
     * Define column type.
     */
    public $db_type = 'F';

    /**
     * Number of digits.
     */
    public $digits     = 10;
    /**
     * Number of decimals.
     */
    public $decimals   = 2;
}
// END Logicoder_Model_Field_Float class

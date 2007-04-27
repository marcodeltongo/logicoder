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
 * Char Field
 *
 * A string field, for small to large strings.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Char extends Logicoder_Model_Field
{
    /**
     * Define column type.
     */
    public $db_type = 'C';

    /**
     * Minimum string length
     */
    public $minlength = 0;

    /**
     * Maximum string length
     */
    public $maxlength = 255;
}
// END Logicoder_Model_Field_Char class

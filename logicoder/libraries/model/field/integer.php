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
 * Integer Field
 *
 * An integer.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Integer extends Logicoder_Model_Field
{
    /**
     * Define column type.
     */
    public $db_type = 'I';

    /**
     * An integer can be negative.
     */
    public $unsigned = false;
}
// END Logicoder_Model_Field_Integer class

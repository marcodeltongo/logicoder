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
class Logicoder_Model_Field_Integer extends Logicoder_Model_Field_Abstract
{
    /**
     * Define column type.
     */
    public $db_type = 'I';

    /**
     * Field is unsigned.
     */
    public $unsigned = false;

    /**
     *  Sanitizes a value for this field.
     *
     *  @param  mixed   $mValue     Value to sanitize
     *
     *  @return integer Sanitized value
     */
    public function sanitize ( $mValue )
    {
        return sanitize_int($mValue);
    }
}
// END Logicoder_Model_Field_Integer class

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
 * Date and Time Field
 *
 * Represents a date and time.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_DateTime extends Logicoder_Model_Field_Date
{
    /**
     * Define column type.
     */
    public $db_type = 'DT';

    /**
     *  Sanitizes a value for this field.
     *
     *  @param  mixed   $mValue     Value to sanitize
     *
     *  @return string  Sanitized value
     */
    public function sanitize ( $mValue )
    {
        return sanitize_isodatetime($mValue);
    }
}
// END Logicoder_Model_Field_DateTime class

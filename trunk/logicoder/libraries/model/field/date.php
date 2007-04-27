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
 * Date Field
 *
 * Represents a date.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Date extends Logicoder_Model_Field
{
    /**
     * Define column type.
     */
    public $db_type = 'D';

    /**
     * Automatically set the field to now everytime the object is saved.
     */
    public $auto_now       = false;

    /**
     * Automatically set the field to now when the object is first created.
     */
    public $auto_now_add   = false;
}
// END Logicoder_Model_Field_Date class

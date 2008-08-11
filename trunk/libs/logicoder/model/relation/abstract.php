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
 * Abstract Model Relation class.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_Model_Relation_Abstract extends Logicoder_Model_Field_Abstract
{
    /**
     * The name for the related model inverse selection.
     *
     * Defined as $Car->manufacturer the related name is $Manufacturer->cars
     */
    public $related_name        = false;

    /**
     * The related model name.
     */
    public $to_model            = false;

    /**
     * The related field name.
     */
    public $to_field            = false;

    /**
     * The related db_column name.
     */
    public $to_column           = false;
}
// END Logicoder_Model_Relation_Abstract class

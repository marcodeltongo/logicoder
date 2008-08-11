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
 * "Many to One" Model Relation class. Aka ForeignKey.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Relation_ManyToOne extends Logicoder_Model_Relation_Abstract
{
    /**
     *
     */
    public $edit_inline         = false;

    /**
     *
     */
    public $limit_choices_to    = false;

    /**
     *
     */
    public $max_num_in_admin    = false;

    /**
     *
     */
    public $min_num_in_admin    = false;

    /**
     *
     */
    public $num_extra_on_change = false;

    /**
     *
     */
    public $num_in_admin        = false;

    /**
     *
     */
    public $raw_id_admin        = false;
}
// END Logicoder_Model_Relation_ManyToOne class

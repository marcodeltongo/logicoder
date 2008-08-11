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
 * "Many to Many" Model Relation class. Uses a middle table.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Relation_ManyToMany extends Logicoder_Model_Relation_Abstract
{
    /**
     *
     */
    public $filter_interface    = false;

    /**
     *
     */
    public $limit_choices_to    = false;

    /**
     *
     */
    public $symmetrical         = false;

    /**
     * Junction table name.
     */
    public $db_table            = false;
}
// END Logicoder_Model_Relation_ManyToMany class

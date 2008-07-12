<?php
/**
 * Logicoder Web Application Framework - Application library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// ------------------------------------------------------------------------------

/**
 * Database models registry.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Junction extends Logicoder_Model
{
    /**
     * M2O relation for M2M FROM field.
     */
    public $from;

    /**
     * M2O relation for M2M TO field.
     */
    public $to;

	/**
	 * Constructor
	 */
    public function __construct ( array $aRelation, array $aFieldsData = array(),
                                  Logicoder_DB_Driver &$oDB = null, Logicoder_Model_Registry &$oReg = null )
    {
		/*
			Import db_table name.
		*/
		$this->__db_table = $aRelation['junction']['table'];
        /*
            Define FROM relation.
        */
        $this->from = array('ManyToOne');
		$this->from[] = $aRelation['from']['model'];
		$this->from['to_field'] = $aRelation['from']['field'];
		$this->from['related_name'] = false;
        /*
            Define TO relation.
        */
        $this->to = array('ManyToOne');
		$this->to[] = $aRelation['to']['model'];
		$this->to['to_field'] = $aRelation['to']['field'];
		$this->to['related_name'] = false;
        /*
            Call parent constructor.
        */
        parent::__construct($aFieldsData, $oDB, $oReg);
    }
}

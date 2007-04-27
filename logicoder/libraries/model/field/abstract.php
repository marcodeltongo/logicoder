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
 * Abstract Model Field class.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_Model_Field_Abstract
{
    /**
     * Parent model.
     */
    public $model;

    /**
     * Field label.
     */
    public $label;

    /**
     * Field name.
     */
    public $db_column;

    /**
     * Field data type.
     */
    public $db_type;

    /**
     * Field can be null.
     */
    public $null             = true;

    /**
     * Field can be blank/empty.
     */
    public $blank            = true;

    /**
     * Limit field to these array of choices.
     */
    public $choices          = false;

    /**
     * This is a core field.
     */
    public $core             = false;

    /**
     * Field should be indexed.
     */
    public $index            = false;

    /**
     * Field default value.
     */
    public $default          = '';

    /**
     * Field can be edited.
     */
    public $editable         = true;

    /**
     * Field help text.
     */
    public $help_text        = false;

    /**
     * Field should be unique.
     */
    public $unique           = false;

    /**
     * Field should be unique for this date.
     */
    public $unique_for_date  = false;

    /**
     * Field should be unique for this month.
     */
    public $unique_for_month = false;

    /**
     * Field should be unique for this year.
     */
    public $unique_for_year  = false;

    /**
     * Field should be validated by these array of validators.
     */
    public $validator_list   = false;

    /**
     * Field should be autoincremented.
     */
    public $auto_inc         = false;

    /**
     *  Field is primary key.
     */
    public $primary_key      = false;

    // -------------------------------------------------------------------------

    /**
     *  Constructor.
     */
    public function __construct ( $oModel, $sField, array $aOptions = array() )
    {
        /*
            Save model reference.
        */
        $this->model = $oModel;
        /*
            Column name.
        */
        $this->db_column = $sField;
		/*
            Set passed options, if any.
		*/
		foreach ($aOptions as $k => $v)
		{
            /*
                Prepare key name.
            */
            $k = str_replace(' ', '_', strtolower($k));
            /*
                Set only if defined.
            */
			if (isset($this->$k))
			{
				$this->$k = $v;
			}
		}
        /*
            Prepare label.
        */
        if ($this->label !== false)
        {
            $this->label = $sField;
        }
        /*
            Check if primary key.
        */
        if (isset($aOptions['primary_key']) and $aOptions['primary_key'] === true)
        {
            $this->primary_key = true;
        }
    }
}
// END Logicoder_Model_Field_Abstract class

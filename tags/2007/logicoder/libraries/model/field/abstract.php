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
    public $default          = null;

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
    public function __construct ( $sField, array $aOptions = array() )
    {
        /*
            Column name.
        */
        $this->db_column = $sField;
		/*
            Set passed options, if any.
		*/
        $aVars = get_object_vars($this);
		foreach ($aOptions as $k => $v)
		{
            /*
                Prepare key name.
            */
            $k = strtolower($k);
            /*
                Set only if defined.
            */
			if (in_array($k, $aVars))
			{
				$this->$k = $v;
			}
            else
            {
                throw new Logicoder_Model_Exception("Unknown property '$k' = $v");
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

    /**
     *  Validates a value for this field.
     *
     *  @param  mixed   $mValue     Value to validate
     *
     *  @return boolean True if valid, false otherwise
     */
    public function validate ( $mValue )
    {
        return true;
    }

    /**
     *  Sanitizes a value for this field.
     *
     *  @param  mixed   $mValue     Value to sanitize
     *
     *  @return mixed   Sanitized value
     */
    public function sanitize ( $mValue )
    {
        return $mValue;
    }
}
// END Logicoder_Model_Field_Abstract class

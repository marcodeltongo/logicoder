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
 * Define fields class prefix.
 */
if (!defined('MODEL_FIELD_PREFIX'))
{
    define('MODEL_FIELD_PREFIX', 'Logicoder_Model_Field_');
}
/**
 * Define fields class suffix.
 */
if (!defined('MODEL_FIELD_SUFFIX'))
{
    define('MODEL_FIELD_SUFFIX', '');
}

// ------------------------------------------------------------------------------

/**
 * Database Model class.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model implements ArrayAccess, Iterator
{
    /**
     *  The current record.
     */
    private $__record                   = null;

    /**
     *  The query builder object.
     */
	private $__query                    = null;

    /**
     *  The db resultset.
     */
	private $__rs                       = null;

    /**
     *  The database driver reference.
     */
	private $__db                       = null;

    /**
     *  Contains the fields for this model.
     */
    private $__fields                   = array();

    /**
     *  The primary key field name.
     */
	private $__pk                       = false;

	/**
	 *	The database table name to use for the model.
	 */
	private $__db_table                 = false;

	/**
	 *	The charset to use for the model data.
	 */
	private $__charset                  = 'utf8';

	/**
	 *	Need to handle file uploads.
	 */
	private $__file_upload              = false;

	/**
	 *	This specifies the default field to use in the latest() method.
	 */
	private $__get_latest_by            = false;

	/**
	 *	Marks this object as "orderable" with respect to the given field.
	 */
	private $__order_with_respect_to    = false;

	/**
	 *	The default ordering for the object.
	 */
	private $__ordering                 = false;

	/**
	 *	Extra permissions to set into the permissions table for this object.
	 */
	private $__permissions              = false;

	/**
	 *	Sets of field names that, taken together, must be unique.
	 */
	private $__unique_together          = false;

	/**
	 *	A human-readable name for the object, singular.
	 */
	private $__verbose_name             = false;

	/**
	 *	A human-readable name for the object, plural.
	 */
	private $__verbose_name_plural      = false;

    // --------------------------------------------------------------------------

	/**
	 * Constructor
	 */
    public function __construct ( array $aOptions = array(), Logicoder_DB_Driver &$oDB = null )
    {
        if (is_null($oDB))
        {
            /*
                Get master reference to DB.
            */
            $this->__db =& Logicoder::instance()->db;
        }
        else
        {
            /*
                Save passed reference to DB driver.
            */
            $this->__db = $oDB;
        }
		/*
            Set passed options, if any.
		*/
		foreach ($aOptions as $k => $v)
		{
            /*
                Prepare key name.
            */
            $k = '__' . str_replace(' ', '_', strtolower($k));
            /*
                Set only if defined.
            */
			if (isset($this->$k))
			{
				$this->$k = $v;
			}
            else
            {
                throw new Logicoder_Model_Exception("Unknown property '$k' = $v");
            }
		}
		/*
            Extract database table name from caller.
		*/
		if ($this->__db_table === false)
		{
            $this->__db_table = strtolower(substr(get_class($this), 0, -6));
		}
		/*
            Extract verbose names from class name.
		*/
        if ($this->__verbose_name === false)
        {
            $this->__verbose_name = str_singular($this->__db_table);
        }
        if ($this->__verbose_name_plural === false)
        {
            $this->__verbose_name_plural = str_plural($this->__verbose_name);
        }
		/*
            Setup fields from *public* properties with array in declaration.
        */
        foreach (get_object_vars($this) as $sField => $aOptions)
        {
            if (!is_array($aOptions) or $sField[0] == '_')
            {
                /*
                    Skip if it's not an array or name starts with an underscore.
                */
                continue;
            }
            else
            {
                /*
                    Remove field from declaration to enforce __magic.
                */
                unset($this->$sField);
            }
            /*
                Take out the class name.
            */
            $sType = strtolower(array_shift($aOptions));
            /*
                Is it a field or a relation ?
            */
            if (in_array($sType, array('foreignkey','manytoone', 'manytomany', 'onetoone')))
            {
                continue;
            }
            else
            {
                /*
                    It's a field !
                */
                $sClass = MODEL_FIELD_PREFIX . $sType . MODEL_FIELD_SUFFIX;
            }
            /*
                Get a new instance.
            */
            $sField = strtolower($sField);
            $oField = new $sClass($sField, $aOptions);
            /*
                Is it the primary key ?
            */
            if ($oField->primary_key)
            {
                /*
                    Save as such and move to the top of the fields array.
                */
                $this->__pk = $sField;
                $this->__fields = array_merge(array($sField => $oField), $this->__fields);
            }
            else
            {
                /*
                    Save in fields array.
                */
                $this->__fields[$sField] = $oField;
            }
        }
		/*
            Create new primary key, if none already defined, with defaults.
		*/
		if ($this->__pk === false)
		{
            $sPKClass = MODEL_FIELD_PREFIX . 'PrimaryKey' . MODEL_FIELD_SUFFIX;
            $oField = new $sPKClass('id');
            $this->__pk = 'id';
            /*
                Move to the top of the fields array.
            */
            $this->__fields = array_merge(array('id' => $oField), $this->__fields);
		}
		/*
            Prepare an empty queryset.
		*/
        $this->__query = $this->__db->sql_builder();
    }

    // -------------------------------------------------------------------------
    //  ArrayAccess interface to field values and property getters and setters.
    // -------------------------------------------------------------------------

    /**
     * Overload magic property setter method.
     *
     * Here we simply set the value if the field is in the definition.
     */
    protected function __set ( $sKey, $mValue )
    {
        if (isset($this->__fields[$sKey]))
        {
            return $this->__record[$sKey] = $mValue;
        }
        else
        {
            throw new Logicoder_Model_Exception("Trying to set unknown field '$sKey' to '$mValue'.");
        }
    }

    /**
     * Overload magic property getter method.
     */
    protected function __get ( $sKey )
    {
        if (isset($this->__fields[$sKey]))
        {
            return (isset($this->__record[$sKey])) ? $this->__record[$sKey] : null;
        }
        else
        {
            throw new Logicoder_Model_Exception("Trying to get unknown field '$sKey'");
        }
    }

    /**
     * Overload magic property checker method.
     */
    protected function __isset ( $sKey )
    {
        if (isset($this->__fields[$sKey]))
        {
            return (isset($this->__record[$sKey]));
        }
        else
        {
            throw new Logicoder_Model_Exception("Trying to check unknown field '$sKey'");
        }
    }

    /**
     * Overload magic property unsetter method.
     */
    protected function __unset ( $sKey )
    {
        if (isset($this->__fields[$sKey]))
        {
            unset($this->__record[$sKey]);
        }
        else
        {
            throw new Logicoder_Model_Exception("Trying to unset unknown field '$sKey'");
        }
    }

    /**
     * Implements ArrayAccess element setter.
     */
    public function offsetSet ( $sKey, $mValue )
    {
        return $this->__set($sKey, $mValue);
    }

    /**
     * Implements ArrayAccess element getter.
     */
    public function offsetGet ( $sKey )
    {
        return $this->__get($sKey);
    }

    /**
     * Implements ArrayAccess element unsetter.
     */
    public function offsetUnset ( $sKey )
    {
        return $this->__unset($sKey);
    }

    /**
     * Implements ArrayAccess element checker.
     */
    public function offsetExists ( $sKey )
    {
        return $this->__isset($sKey);
    }

    // -------------------------------------------------------------------------
    //  Iterator interface to records.
    // -------------------------------------------------------------------------

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind ( /* void */ )
    {
    }

    /**
     * Return the current element.
     */
    public function current ( /* void */ )
    {
    }

    /**
     * Return the key of the current element.
     */
    public function key ( /* void */ )
    {
    }

    /**
     * Move forward to next element.
     */
    public function next ( /* void */ )
    {
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     */
    public function valid ( /* void */ )
    {
    }

    // -------------------------------------------------------------------------
    //  Schema management methods.
    // -------------------------------------------------------------------------

    public function get_ddl_create ( /* void */ )
    {
        $fields = array();
        foreach ($this->__fields as $name => $field)
        {
            $fields[$name] = object_to_array($field);
        }
        return $this->__db->ddl_builder()->create_table($this->__db_table, $fields);
    }

    // -------------------------------------------------------------------------
    //  Cloning and cleaning methods.
    // -------------------------------------------------------------------------

    /**
     * Clean all sql and db related data.
     */
    public function clean ( /* void */ )
    {
        /*
            Reset record info.
        */
        $this->__record = array();
        $this->__query->clean();
        $this->__rs = null;
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Magic __clone.
     */
    public function __clone ( /* void */ )
    {
        /*
            Clone thyself to mantain indipendence.
        */
        $that = clone $this;
        /*
            Return a clean clone.
        */
        return $that->clean();
    }

    // -------------------------------------------------------------------------
    //  Single record methods.
    // -------------------------------------------------------------------------

    /**
     * Inserts or updates a record and saves it all in one step.
     */
	public function create ( array $aFieldValues = null )
	{
        /*
            New cloned instance.
        */
        $that = clone $this;
        /*
            Pass values to internals.
        */
        foreach ($aFieldValues as $k => $v)
        {
            $that->__set($k, $v);
        }
        /*
            Save and return.
        */
        return $that->save();
	}

    /**
     * Finds the object matching the given lookup parameters.
     */
	public static function get ( array $aOptions )
	{
	}

    /**
     * Finds the object matching the given primary key value.
     */
	public static function get_by_pk ( $mPK )
	{
	}

    /**
     * Find a record matching the options or create a new one.
     */
	public static function get_or_create ( array $aOptions )
	{
	}

    /**
     * Counts the number of records matching the given lookup parameters.
     */
	public static function count ( array $aOptions )
	{
        #return $this->__query->count()->where($aOptions);
	}

    /**
     * Returns an array of objects for records matching passed primary keys.
     */
	public static function in_bulk ( array $aPKs )
	{
	}

    /**
     * Finds the last record by PK, get_latest_by or passed field.
     */
	public static function latest ( $sField = null )
	{
	}

    // -------------------------------------------------------------------------
    //  Multiple records methods.
    // -------------------------------------------------------------------------

    /**
     * Return a resultset with all records.
     */
	public function all ( /* void */ )
	{
	}

    /**
     * Return a resultset with filtered records.
     */
	public function filter ( array $aOptions )
	{
	}

    /**
     * Return a resultset without filtered records.
     */
	public function exclude ( array $aOptions )
	{
	}

    // -------------------------------------------------------------------------
    //  Lower level methods.
    // -------------------------------------------------------------------------

    /**
     * Insert a new record with current data.
     *
     * @return  boolean True on success or false on failure
     */
    public function insert ( /* void */ )
    {
        /*
            Prepare placeholders.
        */
        $aPlaceholders = array();
        foreach ($this->__record as $k => $v)
        {
            $aPlaceholders[$k] = ':' . $k;
        }
        /*
            Prepare queryset.
        */
        $qs = $this->__query->insert($aPlaceholders, $this->__db_table);
        /*
            Run insert.
        */
        $rs = $this->__db->execute($qs, $this->__record);
        /*
            If OK then update local data.
        */
        $qs = $this->__query->select()->from($this->__db_table)->where($this->__pk, $this->__db->inserted_id());
        $this->__record = $this->__db->query_row($qs);
        return $rs;
    }

    /**
     * Update a record with current data.
     *
     * @return  boolean True on success or false on failure
     */
    public function update ( /* void */ )
    {
        /*
            Prepare placeholders.
        */
        $aPlaceholders = array();
        foreach ($this->__record as $k => $v)
        {
            if ($k == $this->__pk) continue;
            $aPlaceholders[$k] = ':' . $k;
        }
        /*
            Prepare queryset.
        */
        $qs = $this->__query->update($this->__db_table, $aPlaceholders)->where($this->__pk, ':'.$this->__pk);
        /*
            Run insert.
        */
        return $this->__db->execute($qs, $this->__record);
    }

    /**
     * Save a record with current data.
     *
     * @return  boolean True on success or false on failure
     */
    public function save ( /* void */ )
    {
        $qs = $this->__query->count()->from($this->__db_table)->where($this->__pk, ':'.$this->__pk);
        /*
            Already existing or new ?
        */
        if (isset($this[$this->__pk]) and
            $this->__db->query_col($qs, array($this->__pk => $this[$this->__pk])) == 1)
        {
            return $this->update();
        }
        else
        {
            return $this->insert();
        }
    }
}
// END Logicoder_Model class

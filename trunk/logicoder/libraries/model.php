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
     * The current record.
     */
    protected $__record                 = false;

    /**
     * The query builder object.
     */
	protected $__query                  = null;

    /**
     * The db resultset.
     */
	protected $__rs                     = null;

    /**
     * The database driver reference.
     */
	protected $__db                     = null;

    /**
     * The models manager reference.
     */
	protected $__manager               = null;

    /**
     * Contains this model fields.
     */
    protected $__fields                 = array();

    /**
     * The filters data array.
     */
	protected $__filters_data           = array();

    /**
     *  The primary key field name.
     */
	protected $__pk_field               = false;

    /**
     *  The primary key column name.
     */
	protected $__pk                     = false;

	/**
	 *	The database table name to use for the model.
	 */
	protected $__db_table               = false;

	/**
	 *	The charset to use for the model data.
	 */
	protected $__charset                = 'utf8';

	/**
	 *	Need to handle file uploads.
	 */
	protected $__file_upload            = false;

	/**
	 *	This specifies the default field to use in the latest() method.
	 */
	protected $__get_latest_by          = false;

	/**
	 *	Marks this object as "orderable" with respect to the given field.
	 */
	protected $__order_with_respect_to  = false;

	/**
	 *	The default ordering for the object.
	 */
	protected $__ordering               = false;

	/**
	 *	Extra permissions to set into the permissions table for this object.
	 */
	protected $__permissions            = false;

	/**
	 *	Sets of field names that, taken together, must be unique.
	 */
	protected $__unique_together        = false;

	/**
	 *	A human-readable name for the object, singular.
	 */
	protected $__verbose_name           = false;

	/**
	 *	A human-readable name for the object, plural.
	 */
	protected $__verbose_name_plural    = false;

    // --------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param   array   $aFieldsData    Preset values for fields
	 * @param   object  $oDB            A Logicoder_DB_Driver instance
	 * @param   object  $oReg           A Logicoder_Model_Registry instance
	 */
    public function __construct ( array $aFieldsData = array(),
                                  Logicoder_DB_Driver &$oDB = null,
                                  Logicoder_Model_Registry &$oReg = null )
    {
        /*
            Get reference to DB driver.
        */
        if (is_null($oDB))
        {
            $this->__db =& Logicoder::instance()->db;
        }
        else
        {
            $this->__db = $oDB;
        }
        /*
            Get reference to models registry.
        */
        if (is_null($oReg))
        {
            $this->__manager =& Logicoder::instance()->models;
        }
        else
        {
            $this->__manager = $oReg;
        }
		/*
            Extract database table name from caller.
		*/
		if ($this->__db_table === false)
		{
            $db_table = (defined('APP_NAME')) ? APP_NAME . '_' : '';
            $db_table .= strtolower(substr(get_class($this), 0, -6));
            $this->__db_table = str_plural($db_table);
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
        $aRelations = array();
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
                Take out the class name and lower the field name for simplicity.
            */
            $sType = strtolower(array_shift($aOptions));
            $sField = strtolower($sField);
            /*
                Detect field/relation type.
            */
            if (in_array($sType, array('fk', 'm2o', 'manytoone', 'foreignkey', 'm2m', 'manytomany', 'o2o', 'onetoone', 'extend')))
            {
                /*
                    Save for late build.
                */
                $aRelations[$sField] = array($sType, $aOptions);
                continue;
            }
            /*
                It's a field !
            */
            $this->__setup_field($sField, $sType, $aOptions);
        }
		/*
            Create new primary key, if none already defined, with defaults.
		*/
		if ($this->__pk_field === false)
		{
            $sPKClass = MODEL_FIELD_PREFIX . 'PrimaryKey' . MODEL_FIELD_SUFFIX;
            $oField = new $sPKClass('id');
            $this->__pk_field = 'id';
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
        /*
            Build relational fields.
        */
        foreach ($aRelations as $sField => $aData)
        {
            $this->__setup_relation($sField, $aData[0], $aData[1]);
        }
		/*
            Register to manager.
		*/
        $this->__manager->register($this);
		/*
            If fields data is passed, prepare a record object.
		*/
        foreach ($aFieldsData as $k => $v)
        {
            $this->__set($k, $v);
        }
    }

    /**
     * Setup a field.
     *
     * @param   string  $sField     Field name
     * @param   string  $sType      Field type
     * @param   array   $aOptions   Options
     */
    protected function __setup_field ( $sField, $sType, $aOptions )
    {
        $sClass = MODEL_FIELD_PREFIX . $sType . MODEL_FIELD_SUFFIX;
        /*
            Get a new instance.
        */
        $oField = new $sClass($sField, $aOptions);
        /*
            Is it the primary key ?
        */
        if ($oField->primary_key)
        {
            /*
                Save as such and move to the top of the fields array.
            */
            $this->__pk_field = $sField;
            $this->__pk = $oField->db_column;
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

    /**
     * Setup a relational field.
     *
     * @param   string  $sField     Field name
     * @param   string  $sType      Field type
     * @param   array   $aOptions   Options
     */
    protected function __setup_relation ( $sField, $sType, $aOptions )
    {
        /*
            Extract options.
        */
        if (!isset($aOptions[0]))
        {
            throw new Logicoder_Model_Field_Exception('Missing related model name.');
        }
        /*
            Extract related model.
        */
        $sToModel = array_shift($aOptions);
        $bSelf = (strcasecmp($sToModel, 'self') === 0);
        /*
            Prepare needed objects.
        */
        if ($bSelf)
        {
            $aOptions['to_model'] = strtolower(get_class($this));
            $oModel = $this;
        }
        else
        {
            $sToModel .= (strtolower(substr($sToModel, -6)) == '_model') ? '' : '_Model';
            $aOptions['to_model'] = strtolower($sToModel);
            $oModel = new $sToModel();
        }
        /*
            Point to primary key if not passed.
        */
        if (!isset($aOptions['to_field']))
        {
            $aOptions['to_field'] = $oModel->get_pk_field();
        }
        /*
            Get pointed field reference.
        */
        $oField = &$oModel->get_field_ref($aOptions['to_field']);
        /*
            Set to_column name.
        */
        $aOptions['to_column'] = $oField->db_column;
        /*
            Set db_column name if not passed.
        */
        if (!isset($aOptions['db_column']))
        {
            $aOptions['db_column'] = ($bSelf) ? 'fk_self_' . $aOptions['to_field']
                                                : 'fk_' . $oModel->get_db_table() . '_' . $aOptions['to_field'];
        }
        /*
            Prepare specific informations.
        */
        switch ($sType)
        {
            case 'o2o':
            case 'extend':
            case 'onetoone':
                $sClass = MODEL_RELATION_ONETOONE;
                /*
                    Override local db_type to be the same as the related field.
                */
                $aOptions['db_type'] = $oField->db_type;
                /*
                    Set related name if not passed.
                */
                if (!isset($aOptions['related_name']))
                {
                    $aOptions['related_name'] = str_singular(strtolower(substr(get_class($this), 0, -6)));
                }
            break;

            case 'fk':
            case 'm2o':
            case 'manytoone':
            case 'foreignkey':
                $sClass = MODEL_RELATION_MANYTOONE;
                /*
                    Override local db_type to be the same as the related field.
                */
                $aOptions['db_type'] = $oField->db_type;
                /*
                    Set related name if not passed.
                */
                if (!isset($aOptions['related_name']))
                {
                    $aOptions['related_name'] = str_plural(strtolower(substr(get_class($this), 0, -6)));
                }
            break;

            case 'm2m':
            case 'manytomany':
                $sClass = MODEL_RELATION_MANYTOMANY;
                /*
                    Set local db_type.
                */
                $aOptions['db_type'] = $this->__fields[$this->__pk_field]->db_type;
                $aOptions['db_column'] = $this->__pk;
                /*
                    Build join table name if not set.
                */
                if (!isset($aOptions['db_table']))
                {
                    /*
                        Make up a junction table name.
                        Note: used a md5 code to limit string length.
                    */
                    $junction = hash('md5', sprintf('%s__%s_|_%s__%s', $this->__db_table, $sField, $oModel->get_db_table(), $aOptions['to_field']));
                    $aOptions['db_table'] = ((defined('APP_NAME')) ? APP_NAME : '') . '_junction_' . $junction;
                }
                /*
                    Set related name if not passed.
                */
                if (!isset($aOptions['related_name']))
                {
                    $aOptions['related_name'] = str_plural(strtolower(substr(get_class($this), 0, -6)));
                }
            break;
        }
        /*
            Save in fields array.
        */
        $this->__fields[$sField] = new $sClass($sField, $aOptions);
    }

    /**
     * Overloadable toString.
     *
     * @return  string  A string representation of the model.
     */
    public function __toString ( /* void */ )
    {
        return '{' . get_class($this) . ' object}';
    }

    /**
     * Overloadable toArray.
     *
     * @return  array   An array representation of the model data.
     */
    public function __toArray ( /* void */ )
    {
        return $this->__record;
    }

    // -------------------------------------------------------------------------
    //  Schema/Fields/Relations methods.
    // -------------------------------------------------------------------------

    /**
     * Returns database table name.
     *
     * @return  string  Database table name
     */
    public function get_db_table ( /* void */ )
    {
        if ($this->__db_table === false)
        {
            throw new Logicoder_Model_Exception("DB table name not set.");
        }
        return $this->__db_table;
    }

    /**
     * Returns model primary key field name.
     *
     * @return  string  Model primary key field name
     */
    public function get_pk_field ( /* void */ )
    {
        if (!isset($this->__pk_field))
        {
            throw new Logicoder_Model_Exception("Primary key not set.");
        }
        return $this->__pk_field;
    }

    /**
     * Returns model primary key column name.
     *
     * @return  string  Model primary key column name
     */
    public function get_pk_column ( /* void */ )
    {
        if (!isset($this->__pk))
        {
            throw new Logicoder_Model_Exception("Primary key not set.");
        }
        return $this->__pk;
    }

    /**
     * Returns a reference to a model field.
     *
     * @param   string  $sField     The field key
     *
     * @return  object  A reference to a model field
     */
    public function &get_field_ref ( $sField )
    {
        if (!isset($this->__fields[$sField]))
        {
            throw new Logicoder_Model_Exception("Trying to get unknown field '$sField'.");
        }
        return $this->__fields[$sField];
    }

    /**
     * Returns all model fields.
     *
     * @return  array   All model fields
     */
    public function get_fields ( /* void */ )
    {
        return $this->__fields;
    }

    // -------------------------------------------------------------------------
    //  ArrayAccess interface to field values and property getters and setters.
    // -------------------------------------------------------------------------

    /**
     * Overload magic property setter method.
     *
     * @param   string  $sKey       The field key
     * @param   mixed   $mValue     The value to set (or model to get value from)
     *
     * @return  mixed   The value set
     */
    protected function __set ( $sKey, $mValue )
    {
        /*
            Manage relations.
        */
        if ($this->__manager->has_relation($this, $sKey))
        {
            return $this->__manager->set_related($this, $sKey, $mValue);
        }
        /*
            It's not a relation, convert field to db_column.
        */
        if (isset($this->__fields[$sKey]))
        {
            $sKey = $this->__fields[$sKey]->db_column;
        }
        /*
            Set value.

            NOTE: Should we add a check for unknown columns ??
        */
        return $this->__record[$sKey] = $mValue;
    }

    /**
     * Overload magic property getter method.
     *
     * @param   string  $sKey       The field key
     *
     * @return  mixed   Value for fields or model instance for relations
     */
    protected function __get ( $sKey )
    {
        /*
            Manage relations.
        */
        if ($this->__manager->has_relation($this, $sKey))
        {
            return $this->__manager->get_related($this, $sKey);
        }
        /*
            It's not a relation, convert field to db_column.
        */
        $default = null;
        if (isset($this->__fields[$sKey]))
        {
            $default = $this->__fields[$sKey]->default;
            $sKey = $this->__fields[$sKey]->db_column;
        }
        /*
            Return value from current record or default.
        */
        if (!isset($this->__record[$sKey]))
        {
            $this->__record[$sKey] = $default;
        }
        return $this->__record[$sKey];
    }

    /**
     * Overload magic property checker method.
     *
     * @param   string  $sKey       The field key
     *
     * @return  boolean True if set, false otherwise
     */
    protected function __isset ( $sKey )
    {
        /*
            Convert field to db_column.
        */
        if (isset($this->__fields[$sKey]))
        {
            $sKey = $this->__fields[$sKey]->db_column;
        }
        return (isset($this->__record[$sKey]));
    }

    /**
     * Overload magic property unsetter method.
     *
     * @param   string  $sKey       The field key
     */
    protected function __unset ( $sKey )
    {
        /*
            Convert field to db_column.
        */
        if (isset($this->__fields[$sKey]))
        {
            $sKey = $this->__fields[$sKey]->db_column;
        }
        unset($this->__record[$sKey]);
    }

    /**
     * Implements ArrayAccess element setter.
     *
     * @param   string  $sKey       The field key
     * @param   mixed   $mValue     The value to set (or model to get value from)
     *
     * @return  mixed   The value set
     */
    public function offsetSet ( $sKey, $mValue )
    {
        return $this->__set($sKey, $mValue);
    }

    /**
     * Implements ArrayAccess element getter.
     *
     * @param   string  $sKey       The field key
     *
     * @return  mixed   Value for fields or model instance for relations
     */
    public function offsetGet ( $sKey )
    {
        return $this->__get($sKey);
    }

    /**
     * Implements ArrayAccess element unsetter.
     *
     * @param   string  $sKey       The field key
     */
    public function offsetUnset ( $sKey )
    {
        return $this->__unset($sKey);
    }

    /**
     * Implements ArrayAccess element checker.
     *
     * @param   string  $sKey       The field key
     *
     * @return  boolean True if set, false otherwise
     */
    public function offsetExists ( $sKey )
    {
        return $this->__isset($sKey);
    }

    // -------------------------------------------------------------------------
    //  Iterator interface methods implementation.
    // -------------------------------------------------------------------------

    /**
     * This function rewinds the iterator to the beginning.
     */
    public function rewind ( /* void */ )
    {
        /*
            Runs the query, if any.
        */
        if (!is_null($this->__query->sql()))
        {
            $qs = $this->__query->select()->from($this->__db_table);
            $this->__rs = $this->__db->query($qs, $this->__filters_data);
            $this->__record = $this->__rs->row();
        }
        else
        {
            $this->__rs = null;
            $this->__record = false;
        }
    }

    /**
     * This function returns the current record object.
     *
     * @return  mixed   Returns the current record object
     */
    public function current ( /* void */ )
    {
        /*
            Return current record.
        */
        return $this;
    }

    /**
     * This function returns the current record object key.
     *
     * @return  mixed   Returns the current record key
     */
    public function key ( /* void */ )
    {
        /*
            The key is the primary key.
        */
        return $this->__record[$this->__pk];
    }

    /**
     * This function moves the iterator to the next entry.
     */
    public function next ( /* void */ )
    {
        /*
            Get a new row or false.
        */
        $this->__record = $this->__rs->row();
    }

    /**
     * This function checks if the array contains any more entries.
     *
     * @return  boolean True if there are more records, false otherwise.
     */
    public function valid ( /* void */ )
    {
        /*
            Check for statement and record.
        */
        return !(is_null($this->__rs) or ($this->__record === false));
    }

    // -------------------------------------------------------------------------
    //  Cloning and cleaning methods.
    // -------------------------------------------------------------------------

    /**
     * Magic clone method.
     */
    public function __clone ( /* void */ )
    {
        /*
            Clone query sub-object also.
        */
        $this->__query = clone $this->__query;
    }

    /**
     * Clean all sql and db related data.
     */
    public function clean ( /* void */ )
    {
        /*
            Reset record info.
        */
        $this->__filters_data = array();
        $this->__record = array();
        $this->__query->clean();
        $this->__rs = null;
        /*
            Return for method chaining.
        */
        return $this;
    }

    /**
     * Prepares a clone, cleans internals and eventually sets fields values.
	 *
	 * @param   array   $aFieldsData    Preset values for fields
	 *
	 * @return  object  A cleaned clone of this model
     */
    protected function __prepare_clone ( array $aFieldsData = array() )
    {
        $that = clone $this;
        $that->clean();
        /*
            Save fields values.
		*/
        foreach ($aFieldsData as $k => $v)
        {
            $that->__set($k, $v);
        }
        return $that;
    }

    // -------------------------------------------------------------------------
    //  Filtering methods.
    // -------------------------------------------------------------------------

    /**
     * Overloaded magic function, apply filter if found.
     *
     * @param   string  $sFilter    Field and Filter names
     * @param   array   $aParams    Values for filters
     *
     * @return  object  Clone for method chaining
     */
    public function __call ( $sFilter, $aParams )
    {
        $that = clone $this;
        /*
            Pre-filter field name.
        */
        if (stripos($sFilter, 'pk__') !== false)
        {
            /*
                Check for special pk__ key.
            */
            $aPatterns = array('|^(pk__)|i','|(__pk__)|i');
            $aSubstitues = array($that->__pk_field.'__', '__'.$that->__pk_field.'__', );
            $sFilter = preg_replace($aPatterns, $aSubstitues, $sFilter);
        }
        else
        {
            /*
                Try to get filter informations.
            */
            if (($aInfo = $that->__query->__filter_info($sFilter)) !== false)
            {
                /*
                    Check field exists.
                */
                if (!isset($that->__fields[$aInfo['_field']]))
                {
                    throw new Logicoder_Model_Exception("Wrong field for filter.");
                }
                elseif ($aInfo['_table'] === false)
                {
                    /*
                        It's a local column.
                    */
                    $sColumn = $that->__fields[$aInfo['_field']]->db_column;
                    $sFilter = str_replace($aInfo['_field'], $sColumn, $sFilter);
                }
                else
                {
                    /*
                        It's a column of another table. Mhmm... do nothing for now.
    
                        This probably should be translated as a Model not a table.
                    */
                }
            }
            elseif (isset($that->__fields[$sFilter]))
            {
                /*
                    It's not a filter, user wants the field value.
                */
                return $that->get()->$sFilter;
            }
            else
            {
                throw new Logicoder_Model_Exception("Unknown filter/field '$sFilter'.");
            }
        }
        /*
            Prepare placeholders and save values.
        */
        $aPlaceholders = array();
        switch (count($aParams))
        {
            case 0:
                /*
                    Nothing to do, but here to simplify switch.
                */
            break;

            case 1:
                /*
                    One value, one placeholder.
                */
                $sKey = ':' . $sFilter . count($that->__filters_data);
                $that->__filters_data[$sKey] = $aParams[0];
                $aPlaceholders[] = $sKey;
            break;

            default:
                /*
                    If we got here, there are more than one values.
                */
                foreach ($aParams as $mParam)
                {
                    $sKey = ':' . $sFilter . count($that->__filters_data);
                    $that->__filters_data[$sKey] = $mParam;
                    $aPlaceholders[] = $sKey;
                }
        }
        /*
            Add placeholders and local values management.
        */
        call_user_func_array(array(&$that->__query, $sFilter), $aPlaceholders);
        /*
            Return clone for method chaining.
        */
        return $that;
    }

    // -------------------------------------------------------------------------
    //  Record methods.
    // -------------------------------------------------------------------------

    /**
     * Limits the resultset.
     *
     * @param   integer $nLimit     Maximum number of records to return
     * @param   integer $nOffset    Starting record offset (defaults to false)
     *
     * @return  object  Instance for method chaining
     */
	public function limit ( $nLimit, $nOffset = false )
	{
        $this->__query->limit($nLimit, $nOffset);
        /*
            Return for method chaining.
        */
        return $this;
	}

    /**
     * Limits the resultset.
     *
     * @param   integer $nLimit     Maximum number of records to return
     * @param   integer $nOffset    Starting record offset (defaults to false)
     *
     * @return  object  Instance for method chaining
     */
	public function order_by ( $nLimit, $nOffset = false )
	{
        $this->__query->limit($nLimit, $nOffset);
        /*
            Return for method chaining.
        */
        return $this;
	}

    /**
     * Return a resultset with all records.
     *
     * @return  array   Array of model clones for all records in the table
     */
	public function all ( /* void */ )
	{
        $aRecords = array();
        foreach ($this->__db->get($this->__db_table) as $row)
        {
            $aRecords[$row[$this->__pk]] = $this->__prepare_clone($row);
        }
        return $aRecords;
	}

    /**
     * Inserts or updates a record and saves it all in one step.
	 *
	 * @param   array   $aFieldsData    Preset values for fields
     *
     * @return  object  Clone for method chaining
     */
	public function create ( array $aFieldsData )
	{
        $that = $this->__prepare_clone($aFieldsData);
        $that->save();
        /*
            Return cloned object.
        */
        return $that;
	}

    /**
     * Finds the object matching the given lookup parameters OR simply run the query.
	 *
	 * @param   array   $aFieldsData    Values for searched record
     *
     * @return  object  Clone for method chaining or queried instance
     */
	public function get ( array $aFieldsData = null )
	{
        /*
            If no data passed, run the query.
        */
        if (is_null($aFieldsData))
        {
            $this->rewind();
            return $this;
        }
        /*
            Prepare a clone.
        */
        $that = clone $this;
        $that->clean();
        /*
            Build a query with passed values.
        */
        foreach ($aFieldsData as $k => $v)
        {
            $that = $that->__call($k . '__is', array($v));
        }
        /*
            Check for record.
        */
        $that->__query->count()->from($that->__db_table);
        switch ($that->__db->query_col($that->__query, $that->__filters_data))
        {
            case 1:
                /*
                    Retrieve record.
                */
                $that->__record = $that->__db->query_row($that->__query->select(), $that->__filters_data);
                return $that;
            break;

            case 0:
                throw new Logicoder_Model_RecordNotExists_Exception();
            break;

            default:
                throw new Logicoder_Model_RecordNonUnique_Exception();
            break;
        }
	}

    /**
     * Returns found records as array of values.
	 *
	 * @param   array   $aFieldsData    Values for searched record
	 *
	 * @return  array   Multi-array of records values
     */
	public function values ( array $aFieldsData = null )
	{
        $aRet = array();
        foreach ($this->get($aFieldsData) as $record)
        {
            $aRet[$record[$this->__pk]] = $record->__toArray();
        }
        return $aRet;
	}

    /**
     * Finds a record matching the options or create a new one.
	 *
	 * @param   array   $aFieldsData    Values for searched record
     *
     * @return  object  Clone for method chaining
     */
	public function get_or_create ( array $aFieldsData )
	{
        try
        {
            return $this->get($aFieldsData);
        }
        catch ( Logicoder_Model_RecordNotExists_Exception $e )
        {
            return $this->create($aFieldsData);
        }
	}

    /**
     * Finds the object matching the given primary key value.
     *
     * @param   mixed   $mPK    Primary key value
     *
     * @return  object  Clone for method chaining
     */
	public function get_by_pk ( $mPK )
	{
        $that = $this->__prepare_clone();
        /*
            PK sanity check.
        */
        if (is_null($mPK))
        {
            return $that;
        }
        /*
            Check for record.
        */
        $qs = $that->__query->count()->from($that->__db_table)->where($that->__pk, ':'.$that->__pk);
        switch ($that->__db->query_col($qs, array($that->__pk => $mPK)))
        {
            case 1:
                /*
                    Retrieve by PK.
                */
                $qs = $that->__query->clean()->select()->from($that->__db_table)->where($that->__pk, $mPK);
                $that->__record = $that->__db->query_row($qs);
                return $that;
            break;

            case 0:
                throw new Logicoder_Model_RecordNotExists_Exception("The requested record with {$that->__pk} of $mPK don't exists.");
            break;

            default:
                throw new Logicoder_Model_RecordNonUnique_Exception("The passed {$that->__pk} of $mPK isn't unique.");
            break;
        }
	}

    /**
     * Counts the number of records matching the filters.
     *
     * @param   integer Number of records matching the filters
     */
	public function count ( /* void */ )
	{
        $that = clone $this;
        if (is_null($that->__query->sql()))
        {
            return $that->__db->query_col($that->__query->count()->from($that->__db_table));
        }
        else
        {
            $that->__query->count();
            return $that->__db->query_col($that->__query->count()->from($that->__db_table), $that->__filters_data);
        }
	}

    /**
     * Returns an array of objects for records matching passed primary keys.
     *
     * @param   array   $aPKs       Array of primary keys
     *
     * @return  array   Array of found records
     */
	public function in_bulk ( array $aPKs )
	{
        $aRecords = array();
        foreach ($aPKs as $pk)
        {
            $aRecords[$pk] = $this->get_by_pk($pk);
        }
        return $aRecords;
	}

    /**
     * Finds the last record by PK, get_latest_by or passed field.
     */
	public function latest ( $sField = null )
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
        $qs = $this->__query->clean()->insert($aPlaceholders, $this->__db_table);
        /*
            Run insert.
        */
        $rs = $this->__db->execute($qs, $this->__record);
        /*
            Update local data.
        */
        $qs = $this->__query->clean()->select()->from($this->__db_table)->where($this->__pk, $this->__db->inserted_id());
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
        $qs = $this->__query->clean()->update($this->__db_table, $aPlaceholders)->where($this->__pk, ':'.$this->__pk);
        /*
            Run update.
        */
        $rs = $this->__db->execute($qs, $this->__record);
        /*
            Update local data.
        */
        $qs = $this->__query->clean()->select()->from($this->__db_table)->where($this->__pk, $this[$this->__pk]);
        $this->__record = $this->__db->query_row($qs);
        return $rs;
    }

    /**
     * Save a record with current data.
     *
     * @return  boolean True on success or false on failure
     */
    public function save ( /* void */ )
    {
        $qs = $this->__query->clean()->count()->from($this->__db_table)->where($this->__pk, ':'.$this->__pk);
        /*
            Already existing or new ?
        */
        if (isset($this->__record[$this->__pk]) and
            $this->__db->query_col($qs, array($this->__pk => $this->__record[$this->__pk])) == 1)
        {
            return $this->update();
        }
        else
        {
            return $this->insert();
        }
    }

    // -------------------------------------------------------------------------
    //  Schema methods.
    // -------------------------------------------------------------------------

    /**
     * Build a DDL schema for the model.
     * 
     * @param   boolean $bIfNotExists   True to create only if not exists
     * @param   boolean $bDrop          True to drop before creation
     *
     * @return  string  DDL operations to build the schema
     */
    public function get_create_table ( $bIfNotExists = false, $bDrop = false )
    {
        $fields = array();
        foreach ($this->__fields as $name => $field)
        {
            if (($name != $this->__pk_field) and ($field->db_column == $this->__pk))
            {
                /*
                    Skip M2M fields.
                */
                continue;
            }
            $fields[$name] = object_to_array($field);
        }
        return $this->__db->ddl_builder()->create_table($this->__db_table, $fields, $bIfNotExists, $bDrop);
    }
}
// END Logicoder_Model class

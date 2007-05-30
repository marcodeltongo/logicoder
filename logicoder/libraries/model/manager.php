<?php
/**
 * Logicoder Web Application Framework - Models manager library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// ------------------------------------------------------------------------------

/**
 * Database models manager.
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Manager implements Logicoder_iSingleton, ArrayAccess
{
    /**
     * Models container.
     */
    protected $aModels      = array();

    /**
     * Relations container.
     */
    protected $aRelations   = array();

    /**
     * The singleton instance.
     */
    private static $oInstance = null;

    // -------------------------------------------------------------------------
    //  Singleton interface implementation.
    // -------------------------------------------------------------------------

    /**
     * Private Constructor, don't call it.
     */
    private function __construct ( /* void */ ) { /* void */ }

    /**
     *  Returns the singleton instance of the class.
     *
     *  @return object  The singleton instance
     */
    public static function instance ( /* void */ )
    {
        if (self::$oInstance === null)
        {
            self::$oInstance = new Logicoder_Model_Manager();
        }
        return self::$oInstance;
    }

    // -------------------------------------------------------------------------
    //  Registry-like interface implementation.
    // -------------------------------------------------------------------------

    /**
     * Save the passed model and register it's relations.
     *
     * @param   object  $oModel     The model to register
     * @param   string  $sModel     The name for the model, defaults to class
     * @param   boolean $bOverwrite Whether to overwrite existing model
     *
     * @return  mixed   Key name on success or false on failure
     */
    public function register ( Logicoder_Model $oModel, $sModel = null, $bOverwrite = false )
    {
        $sModel = (is_null($sModel)) ? strtolower(get_class($oModel)) : $sModel;
        /*
            If told to overwrite, unregister first.
        */
        if ($bOverwrite)
        {
            $this->unregister($sModel);
        }
        /*
            Set only first time.
        */
        if (!isset($this->aModels[$sModel]))
        {
            $this->aModels[$sModel] = $oModel;
            $this->aRelations[$sModel] = array();
            /*
                Loop on fields for relations.
            */
            foreach ($oModel->get_fields() as $sField => $oField)
            {
                if ($oField instanceof Logicoder_Model_Relation_Abstract)
                {
                    $this->register_relation($sModel, $oModel, $sField, $oField);
                }
            }
            return $sModel;
        }
        return false;
    }

    /**
     * Destroy $sKey value in the registry.
     *
     * @param   string  $sKey       The model key
     */
    public function unregister ( $sKey )
    {
        $sKey = strtolower($sKey);
        unset($this->aModels[$sKey]);
        unset($this->aRelations[$sKey]);
    }

    /**
     * Returns the $sKey model from the registry.
     *
     * @param   string  $sKey       The model key
     *
     * @return  mixed   A clean object clone on success
     */
    public function get ( $sKey )
    {
        $sKey = strtolower($sKey);
        $sKey .= (substr($sKey, -6) == '_model') ? '' : '_model';
        /*
            If not set throw an exception.
        */
        if (!isset($this->aModels[$sKey]))
        {
            throw new Logicoder_Model_Exception("Unknown model '$sKey'.");
        }
        $oRet = clone $this->aModels[$sKey];
        return $oRet->clean();
    }

    /**
     * Returns true if $sKey model is in the registry.
     *
     * @param   string  $sKey       The model key
     *
     * @return  boolean True if $sKey model is in the registry, false otherwise
     */
    public function has ( $sKey )
    {
        $sKey = strtolower($sKey);
        return isset($this->aModels[$sKey]);
    }

    // -------------------------------------------------------------------------
    //  Relations management.
    // -------------------------------------------------------------------------

    /**
     * Save a relation details.
     *
     * @param   string  $sModel     Model key
     * @param   object  $oModel     Model instance
     * @param   string  $sField     Field key
     * @param   object  $oField     Field instance
     */
    protected function register_relation ( $sModel, Logicoder_Model $oModel,
                                           $sField, Logicoder_Model_Relation_Abstract $oField )
    {
        /*
            Check for duplicates.
        */
        if (isset($this->aRelations[$sModel][$sField]))
        {
            throw new Logicoder_Model_Exception("Field relation duplication.");
        }
        /*
            Save common relation information.
        */
        $aRel = array('source' => true, 'type' => get_class($oField));
        $aRel['from'] = array('model' => $sModel, 'field' => $sField, 'column' => $oField->db_column);
        $aRel['to'] = array('model' => $oField->to_model, 'field' => $oField->to_field, 'column' => $oField->to_column);
        /*
            Manage M2M relations.
        */
        if ($aRel['type'] == MODEL_RELATION_MANYTOMANY)
        {
            /*
                Add more info for ManyToMany relations.
            */
            $aRel['junction'] = array();
            $aRel['junction']['table'] = $oField->db_table;
            $aRel['junction']['class'] = $sModel . '_M2M_' . $oField->to_model;
            /*
                Create class on the fly.
            */
            eval('class ' . $aRel['junction']['class'] . ' extends Logicoder_Model_Junction {}');
            $aRel['junction']['instance'] = new $aRel['junction']['class']($aRel);
        }
        /*
            Save source relation.
        */
        $this->aRelations[$sModel][$sField] = $aRel;
        /*
            Save related model relation.
        */
        if ($oField->related_name !== false)
        {
            $aRel['source'] = false;
            $this->aRelations[$oField->to_model][$oField->related_name] = $aRel;
        }
    }

    /**
     * Destroy model relations in the registry.
     *
     * @param   mixed   $mModel     The model key or instance
     */
    protected function unregister_relations ( $mModel )
    {
        $sModel = strtolower((is_string($mModel)) ? $mModel : get_class($mModel));
        $this->aRelations[$sModel] = array();
    }

    /**
     * Destroy a single model relation in the registry.
     *
     * @param   mixed   $mModel     The model key or instance
     * @param   string  $sField     The field key
     */
    protected function unregister_relation ( $mModel, $sField )
    {
        $sModel = strtolower((is_string($mModel)) ? $mModel : get_class($mModel));
        unset($this->aRelations[$sModel][$sField]);
    }

    /**
     * Returns the model relations from the registry.
     *
     * @param   mixed   $mModel     The model key or instance
     *
     * @return  array   The model relations from the registry
     */
    public function get_relations ( $mModel )
    {
        $sModel = strtolower((is_string($mModel)) ? $mModel : get_class($mModel));
        return $this->aRelations[$sModel];
    }

    /**
     * Returns the model relations from the registry.
     *
     * @param   mixed   $mModel     The model key or instance
     * @param   string  $sField     The field key
     *
     * @return  mixed   The model relation from the registry or false
     */
    public function get_relation ( $mModel, $sField )
    {
        $sModel = strtolower((is_string($mModel)) ? $mModel : get_class($mModel));
        if (isset($this->aRelations[$sModel][$sField]))
        {
            return $this->aRelations[$sModel][$sField];
        }
        return false;
    }

    /**
     * Returns true if model has relations.
     *
     * @param   mixed   $mModel     The model key or instance
     *
     * @return  boolean True if model has relations, false otherwise
     */
    public function has_relations ( $mModel )
    {
        $sModel = strtolower((is_string($mModel)) ? $mModel : get_class($mModel));
        return !empty($this->aRelations[$sModel]);
    }

    /**
     * Returns true if a model field has relation.
     *
     * @param   mixed   $mModel     The model key or instance
     * @param   string  $sField     The field key
     *
     * @return  boolean True if field has relation, false otherwise
     */
    public function has_relation ( $mModel, $sField )
    {
        $sModel = strtolower((is_string($mModel)) ? $mModel : get_class($mModel));
        return isset($this->aRelations[$sModel][$sField]);
    }

    // -------------------------------------------------------------------------
    //  Relations DB operations.
    // -------------------------------------------------------------------------

    /**
     * Gets related object(s).
     *
     * @param   object  $oModel     The model instance
     * @param   string  $sField     The field key
     * @param   array   $aRel       Forced relation information
     *
     * @return  object  Returns a model instance or null
     */
    public function get_related ( Logicoder_Model &$oModel, $sField, $aRel = null )
    {
        $sModel = strtolower(get_class($oModel));
        $aRel = (is_null($aRel)) ? $this->aRelations[$sModel][$sField] : $aRel;
        /*
            Work out by type.
        */
        switch ($aRel['type'])
        {
            case MODEL_RELATION_ONETOONE:
                /*
                    Setup.
                */
                if ($aRel['source'])
                {
                    $dataModel = $this->get($aRel['to']['model']);
                    $dataField = $aRel['to']['column'];
                    $dataValue = $oModel->$aRel['from']['column'];
                }
                else
                {
                    $dataModel = $this->get($aRel['from']['model']);
                    $dataField = $aRel['from']['column'];
                    $dataValue = $oModel->$aRel['to']['column'];
                }
                /*
                    Try to get a related object record.
                */
                try {
                    return $dataModel->get(array($dataField => $dataValue));
                }
                catch ( Logicoder_Model_Exception $e )
                {
                    if (($e instanceof Logicoder_Model_RecordNotExists_Exception) or
                        ($e instanceof Logicoder_Model_RecordNotUnique_Exception))
                    {
                        /*
                            One or nothing.
                        */
                        return null;
                    }
                    throw $e;
                }
            break;

            case MODEL_RELATION_MANYTOONE:
                if ($aRel['source'])
                {
                    /*
                        We're on the Many side, got to find the One.
                        Redirect as OneToOne relation.
                    */
                    $aRel['type'] = MODEL_RELATION_ONETOONE;
                    return $this->get_related($oModel, $sField, $aRel);
                }
                else
                {
                    /*
                        We're on the One side, got to find the Many.
                        Create a filter on the key field and return.
                    */
                    $dataModel = $this->get($aRel['from']['model']);
                    $dataFilter = $aRel['from']['field'] . '__is';
                    $dataValue = $oModel->$aRel['to']['column'];
                    /*
                        Run filter.
                    */
                    return $dataModel->$dataFilter($dataValue)->get();
                }
            break;

            case MODEL_RELATION_MANYTOMANY:
                $aRel['junction']['instance']->clean();
                if ($aRel['source'])
                {
                    /*
                        Retrieve junction records.
                    */
                    $results = $aRel['junction']['instance']->from__is($oModel->$aRel['from']['column']);
                    /*
                        Build IN filter array.
                    */
                    $ins = array();
                    foreach($results as $v)
                    {
                        $ins[] = $v->to->$aRel['to']['column'];
                    }
                    /*
                        Retrieve related records.
                    */
                    $dataModel = $this->get($aRel['to']['model']);
                    $dataFilter = $aRel['to']['field'] . '__in';
                    /*
                        Run filter.
                    */
                    return call_user_func_array(array($dataModel, $dataFilter), $ins)->get();
                }
                else
                {
                    /*
                        Retrieve junction records.
                    */
                    $results = $aRel['junction']['instance']->to__is($oModel->$aRel['to']['column']);
                    /*
                        Build IN filter array.
                    */
                    $ins = array();
                    foreach($results as $v)
                    {
                        $ins[] = $v->from->$aRel['from']['column'];
                    }
                    /*
                        Retrieve related records.
                    */
                    $dataModel = $this->get($aRel['from']['model']);
                    $dataFilter = $aRel['from']['field'] . '__in';
                    /*
                        Run filter.
                    */
                    return call_user_func_array(array($dataModel, $dataFilter), $ins)->get();
                }
            break;
        }
    }

    /**
     * Sets related objects and returns field value.
     *
     * @param   object  $oModel     The model instance
     * @param   string  $sField     The field key
     * @param   mixed   $mValue     Related model instance or direct value
     * @param   array   $aRel       Forced relation information
     *
     * @return  object  Returns a model instance or null
     */
    public function set_related ( Logicoder_Model &$oModel, $sField, $mValue, $aRel = null )
    {
        $sModel = strtolower(get_class($oModel));
        $aRel = (is_null($aRel)) ? $this->aRelations[$sModel][$sField] : $aRel;
        /*
            Work out by type.
        */
        switch ($aRel['type'])
        {
            case MODEL_RELATION_ONETOONE:
                if ($aRel['source'])
                {
                    if ($mValue instanceof Logicoder_Model)
                    {
                        $mValue = $mValue->$aRel['to']['column'];
                    }
                    return $oModel->$aRel['from']['column'] = $mValue;
                }
                else
                {
                    if ($mValue instanceof Logicoder_Model)
                    {
                        /*
                            Use mValue->from_field.
                        */
                        $from_model = $mValue;
                        $from_field = $aRel['from']['column'];
                    }
                    else
                    {
                        /*
                            Suppose a value is the PK of the model to relate.
                        */
                        $from_model = $this->get($aRel['from']['model'])->get_by_pk($mValue);
                        $from_field = $aRel['from']['column'];
                    }
                    return $from_model->$from_field = $oModel->$aRel['to']['column'];
                }
            break;

            case MODEL_RELATION_MANYTOONE:
                if ($aRel['source'])
                {
                    /*
                        We're on the Many side, got to set the One.
                        Redirect as OneToOne relation.
                    */
                    $aRel['type'] = MODEL_RELATION_ONETOONE;
                    return $this->set_related($oModel, $sField, $mValue, $aRel);
                }
                else
                {
                    $aRel['type'] = MODEL_RELATION_ONETOONE;
                    if (is_array($mValue))
                    {
                        /*
                            Loop over array of records since there are "Many".
                        */
                        foreach ($mValue as $v)
                        {
                            $this->set_related($oModel, $sField, $v, $aRel);
                        }
                        return;
                    }
                    /*
                        Redirect as OneToOne relation.
                    */
                    return $this->set_related($oModel, $sField, $mValue, $aRel);
                }
            break;

            case MODEL_RELATION_MANYTOMANY:
                $aRel['junction']['instance']->clean();
                if ($aRel['source'])
                {
                    if (is_array($mValue))
                    {
                        /*
                            Loop over array of records since there are "Many".
                        */
                        foreach ($mValue as $v)
                        {
                            $aData = array('from' => $oModel->$aRel['from']['column'], 'to' => $v->$aRel['to']['column']);
                            $aRel['junction']['instance']->get_or_create($aData);
                        }
                        return;
                    }
                    $aData = array('from' => $oModel->$aRel['from']['column'], 'to' => $mValue->$aRel['to']['column']);
                    $aRel['junction']['instance']->get_or_create($aData);
                }
                else
                {
                    if (is_array($mValue))
                    {
                        /*
                            Loop over array of records since there are "Many".
                        */
                        foreach ($mValue as $v)
                        {
                            $aData = array('to' => $oModel->$aRel['from']['column'], 'from' => $v->$aRel['to']['column']);
                            $aRel['junction']['instance']->get_or_create($aData);
                        }
                        return;
                    }
                    $aData = array('to' => $oModel->$aRel['from']['column'], 'from' => $mValue->$aRel['to']['column']);
                    $aRel['junction']['instance']->get_or_create($aData);
                }
            break;
        }
    }

    // -------------------------------------------------------------------------
    //  Schema methods.
    // -------------------------------------------------------------------------

    /**
     * Returns the registered models.
     *
     * @return  array   An array containing the registered models keys
     */
    public function list_models ( /* void */ )
    {
        return array_keys($this->aModels);
    }

    /**
     * Build a DDL schema for a model.
     *
     * @param   string  $sModel         The model key
     * @param   boolean $bIfNotExists   True to create only if not exists
     * @param   boolean $bDrop          True to drop before creation
     *
     * @return  array   An array with the DDL operations to build the schema
     */
    public function get_schema ( $sModel, $bIfNotExists = false, $bDrop = false )
    {
        $db =& Logicoder::instance()->db;
        $ddl = $db->ddl_builder();
        $schema = array();
        /*
            Drop schema from model.
        */
        if ($bDrop)
        {
            $schema[] = $ddl->drop_table($this->aModels[$sModel]->get_db_table(), true);
        }
        /*
            Get schema from model.
        */
        $schema[] = $this->aModels[$sModel]->get_create_table($bIfNotExists, false);
        return $schema;
    }

    /**
     * Build DDL schemas for models.
     *
     * @param   array   $aModels        Array of models keys
     * @param   boolean $bIfNotExists   True to create only if not exists
     * @param   boolean $bDrop          True to drop before creation
     *
     * @return  array   An array with the DDL operations to build the schema
     */
    public function get_schemas ( array $aModels = null, $bIfNotExists = false, $bDrop = false )
    {
        $schemas = array();
        /*
            Gather models to get schemas for.
        */
        $aModels = (is_null($aModels)) ? $this->list_models() : $aModels;
        /*
            Loop on each model.
        */
        foreach ($aModels as $sModel)
        {
            $schemas = array_merge($schemas, $this->get_schema($sModel, $bIfNotExists, $bDrop));
        }
        return $schemas;
    }

    // -------------------------------------------------------------------------
    //  ArrayAccess interface, getters and setters for models.
    // -------------------------------------------------------------------------

    /**
     * Overload magic property setter method.
     */
    protected function __set ( $sModel, Logicoder_Model $oModel )
    {
        $sModel .= (strtolower(substr($sModel, -6)) == '_model') ? '' : '_model';
        return $this->register($oModel, $sModel, true);
    }

    /**
     * Overload magic property getter method.
     */
    protected function __get ( $sModel )
    {
        $sModel .= (strtolower(substr($sModel, -6)) == '_model') ? '' : '_model';
        return $this->get($sModel);
    }

    /**
     * Overload magic property checker method.
     */
    protected function __isset ( $sModel )
    {
        $sModel .= (strtolower(substr($sModel, -6)) == '_model') ? '' : '_model';
        return $this->has($sModel);
    }

    /**
     * Overload magic property unsetter method.
     */
    protected function __unset ( $sModel )
    {
        $sModel .= (strtolower(substr($sModel, -6)) == '_model') ? '' : '_model';
        return $this->unregister($sModel);
    }

    /**
     * Implements ArrayAccess element setter.
     */
    public function offsetSet ( $sModel, $oModel )
    {
        return $this->__set($sModel, $oModel);
    }

    /**
     * Implements ArrayAccess element getter.
     */
    public function offsetGet ( $sModel )
    {
        return $this->__get($sModel);
    }

    /**
     * Implements ArrayAccess element unsetter.
     */
    public function offsetUnset ( $sModel )
    {
        return $this->__unset($sModel);
    }

    /**
     * Implements ArrayAccess element checker.
     */
    public function offsetExists ( $sModel )
    {
        return $this->__isset($sModel);
    }
}
// END Logicoder_Model_Registry class

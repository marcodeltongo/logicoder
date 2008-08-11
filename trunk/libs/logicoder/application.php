<?php
/**
 * Logicoder Web Application Framework - Application library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Application class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/applications.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Application
{
    /**
     * Reference to Settings provider object.
     */
    protected $oData;

    /**
     * Constructor.
     *
     * @param   object  $oSettings  An instance of Logicoder_Settings
     */
    public function __construct ( Logicoder_Settings $oSettings = null )
    {
        /*
            Save reference to Settings instance.
        */
        if (!is_null($oSettings))
        {
            $this->oData = $oSettings;
        }
        elseif (class_exists('Logicoder'))
        {
            $this->oData = Logicoder::instance()->settings;
        }
        else
        {
            $this->oData = new Logicoder_Settings();
        }
    }

    /**
     * Overload magic property setter function.
     *
     * @param   string  $sName      The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value
     */
    protected function __set ( $sKey, $mValue )
    {
        return $this->oData->set('APP', $sKey, $mValue);
    }

    /**
     * Overload magic property getter function.
     *
     * @param   string  $sName      The name/key string
     *
     * @return  mixed   The key value or null
     */
    protected function __get ( $sKey )
    {
        return $this->oData->get('APP', $sKey);
    }

    /**
     * Overload magic property checker function.
     *
     * @param   string  $sName      The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    protected function __isset ( $sKey )
    {
        return $this->oData->get('APP', $sKey) !== null;
    }

    /**
     * Overload magic property unsetter function.
     *
     * @param   string  $sName      The name/key string
     */
    protected function __unset ( $sKey )
    {
        $this->oData->set('APP', $sKey, null);
    }
}
// END Logicoder_Application class

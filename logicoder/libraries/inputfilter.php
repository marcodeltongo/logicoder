<?php
/**
 * Logicoder Web Application Framework - Input filtering library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Input filtering class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/inputfilter.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_InputFilter extends Logicoder_OverArray
{
    /**
     * Superglobal source array.
     */
    protected $aSource;

    // -------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @param   string  $sSource        Global source input array name
     * @param   boolean $bPreprocess    Whether to preprocess source data or not
     */
    public function __construct ( $sSource, $bPreprocess = false )
    {
        /*
            Load sanitize helper dependency.
        */
        if (class_exists('Logicoder'))
        {
            Logicoder::instance()->load->helper('Sanitize');
        }
        else
        {
            require('sanitize.php');
        }
        /*
            Get source
        */
        $this->aSource = (isset($GLOBALS[$sSource])) ? $GLOBALS[$sSource] : array();
        /*
            Pre-process data if required
        */
        if ($bPreprocess)
        {
            foreach ($this->aSource as $sKey => $mValue)
            {
                $this->aData[$sKey] = xss_filter($this->aSource[$sKey]);
            }
        }
    }

    // -------------------------------------------------------------------------
    //  Property getters and setters.
    // -------------------------------------------------------------------------

    /**
     * Overload magic property setter function.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The value
     */
    protected function __set ( $sKey, $mValue )
    {
        $this->aSource[$sKey] = $mValue;
        $this->aData[$sKey] = $mValue;
        return $mValue;
    }

    /**
     * Overload magic property getter function.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The key value
     */
    protected function __get ( $sKey )
    {
        if (!isset($this->aSource[$sKey]))
        {
            return null;
        }
        if (!isset($this->aData[$sKey]))
        {
            $this->aData[$sKey] = (REQUEST_XSS_FILTERING) ?
                                    xss_filter($this->aSource[$sKey]) :
                                    $this->aSource[$sKey];
        }
        return $this->aData[$sKey];
    }

    /**
     * Overload magic property checker function.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    protected function __isset ( $sKey )
    {
        return isset($this->aSource[$sKey]);
    }

    /**
     * Overload magic property unsetter function.
     *
     * @param   string  $sKey       The name/key string
     */
    protected function __unset ( $sKey )
    {
        unset($this->aSource[$sKey]);
        unset($this->aData[$sKey]);
    }
}
// END Logicoder_InputFilter class

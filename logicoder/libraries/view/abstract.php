<?php
/**
 * Logicoder Web Application Framework - Abstract View
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Abstract View class.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
abstract class Logicoder_View_Abstract extends Logicoder_OverArray
{
    /**
     * View source.
     */
    protected $sSource;

    /**
     * Parsed view.
     */
    protected $sParsed;

    /**
     * Source file name for VIEW_FROM_FILE requests.
     */
    protected $sFilename;

    /**
     * DB query to run for VIEW_FROM_QUERY requests.
     */
    protected $sDBQuery;

    /**
     * Method to proxy for class views.
     */
    protected $sMethod;

    /**
     * Constructor.
     *
     * @param   string  $sSource    The view source
     * @param   array   $aData      Data for the view
     */
    public function __construct ( $sSource = null, array $aData = null )
    {
        if (!is_null($sSource))
        {
            /*
                Save passed source.
            */
            $this->source($sSource);
        }
        if (!is_null($aData))
        {
            /*
                Save data.
            */
            $this->data($aData);
        }
    }

    /**
     * Set/get view source as string.
     *
     * @param   string  $sSource    The view source or null to get it
     *
     * @return  mixed   Source if null passed or itself for chaining
     */
    public function source ( $sSource = null )
    {
        if (!is_null($sSource))
        {
            /*
                Save passed source.
            */
            $this->sSource = $sSource;
            /*
                Return myself for method chaining.
            */
            return $this;
        }
        /*
            Return source if called with null parameter.
        */
        return $this->sSource;
    }

    /**
     * Set/get view data as associative array.
     *
     * @param   array   $aData      Data for the view or null to get it
     *
     * @return  mixed   Data if null passed or itself for chaining
     */
    public function data ( array $aData = null )
    {
        if (!is_null($aData))
        {
            /*
                Save passed data.
            */
            return $this->set($aData);
        }
        /*
            Return data if called with null parameter.
        */
        return $this->aData;
    }

    /**
     * Set view data.
     *
     * @param   mixed   $mKey   Associative array or key name
     * @param   mixed   $mVal   Key value
     *
     * @return  object  Itself for chaining
     */
    public function set ( $mKey, $mVal = null )
    {
        if (is_null($mVal) and is_assoc($mKey))
        {
            /*
                If $mVal is null and $mKey is an associative array,
                merge keys and values with internal data container.
            */
            $this->aData = array_merge($this->aData, $mKey);
        }
        else
        {
            /*
                Set key => value pair.
            */
            $this->aData[$mKey] = $mVal;
        }
        /*
            Return myself for method chaining.
        */
        return $this;
    }

    /**
     * Set/get view source SQL query.
     *
     * @param   string  $sSQL   The source SQL query or null to get it
     *
     * @return  mixed   SQL query if null passed or itself for chaining
     */
    public function sql ( $sSQL = null )
    {
        if (!is_null($sSQL))
        {
            /*
                Save passed SQL query.
            */
            $this->sDBQuery = $sSQL;
            /*
                Return myself for method chaining.
            */
            return $this;
        }
        /*
            Return SQL query if called with null parameter.
        */
        return $this->sDBQuery;
    }

    /**
     * Set/get view class method.
     *
     * @param   string  $sMethod    The method to call or null to get it
     *
     * @return  mixed   Method name if null passed or itself for chaining
     */
    public function method ( $sMethod = null )
    {
        if (!is_null($sMethod))
        {
            /*
                Save passed method.
            */
            $this->sMethod = $sMethod;
            /*
                Return myself for method chaining.
            */
            return $this;
        }
        /*
            Return method if called with null parameter.
        */
        return $this->sMethod;
    }

    /**
     * Set/get view source filename.
     *
     * @param   string  $sFilename  The source filename or null to get it
     *
     * @return  mixed   File name if null passed or itself for chaining
     */
    public function filename ( $sFilename = null )
    {
        if (!is_null($sFilename))
        {
            /*
                Save passed method.
            */
            $this->sFilename = $sFilename;
            /*
                Return myself for method chaining.
            */
            return $this;
        }
        /*
            Return filename if called with null parameter.
        */
        return $this->sFilename;
    }

    /**
     * Load a view file.
     *
     * @param   string  $sFilename  The file to load
     *
     * @return  object  Itself for chaining
     */
    public function load ( $sFilename )
    {
        /*
            Get source or throw an exception !
        */
        $this->sFilename = $sFilename;
        if (($sSrc = file_get_contents($sFilename)) === false)
        {
            throw new Logicoder_404('Unable to load view file "' . $sFilename . '"');
        }
        /*
            Save the source and return.
        */
        return $this->source($sSrc);
    }

    /**
     * Run a query against a DB.
     *
     * @param   string  $sSQL   The source SQL query
     *
     * @return  object  Itself for chaining
     */
    public function query ( $sSQL = null )
    {
        if (!is_null($sSQL))
        {
            /*
                Save query.
            */
            $this->sql($sSQL);
        }
        /*
            Run query.
        */
        if (($sSrc = Logicoder::instance()->db->query_col($this->sDBQuery)) === false)
        {
            throw new Logicoder_404('Unable to query view db using "' . $this->sDBQuery . '"');
        }
        /*
            Save the source and return.
        */
        return $this->source($sSrc);
    }

    /**
     * Abstract source parser.
     */
    abstract public function _parse ( /* void */ );

    /**
     * View parser.
     *
     * @param   array   $aData      Data for the view
     *
     * @return  object  Itself for chaining
     */
    public function parse ( array $aData = null )
    {
        if (!is_null($aData))
        {
            /*
                Save data.
            */
            return $this->data($aData);
        }
        /*
            Call real engine.
        */
        $this->_parse();
        /*
            Return for chaining.
        */
        return $this;
    }

    /**
     * Render the view.
     *
     * @param   array   $aData      Data for the view
     * @param   boolean $bOutput    Whether to output view or return it
     *
     * @return  mixed   True for success or output if requested
     */
    public function render ( array $aData, $bOutput = true )
    {
        if (!is_null($aData))
        {
            /*
                Save data.
            */
            return $this->data($aData);
        }
        /*
            Call parser.
        */
        $this->parse($aData);
        /*
            Check we have parsed.
        */
        if (is_null($this->sParsed))
        {
            throw new Logicoder_404('Unable to parse view.');
        }
        /*
            Output or return.
        */
        if ($bOutput)
        {
            echo $this->sParsed;
            return true;
        }
        else
        {
            return $this->sParsed;
        }
    }
}
// END Logicoder_View_Abstract class

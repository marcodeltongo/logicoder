<?php
/**
 * Logicoder Web Application Framework - Settings library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Manages configuration using valid PHP files and optionally CONSTANTS
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/settings.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Settings extends Logicoder_OverArray
{
    /**
     * Master namespace.
     */
    protected $sMaster;

    /**
     * Current namespace.
     */
    protected $sCurrent;

    /**
     * Array of namespace that should define constants.
     */
    protected $aToConstants;

    /**
     * Override namespace definitions.
     */
    protected $bSkipNS;

    /**
     * Constructor
     *
     * @param   string  $sNamespace     It's the namespace
     */
    public function __construct ( $sNamespace = 'Logicoder' )
    {
        $sNamespace         = strtoupper($sNamespace);
        $this->aData        = array($sNamespace => array());
        $this->sCurrent     = $sNamespace;
        $this->sMaster      = $sNamespace;
        $this->aToConstants = array($sNamespace => true);
        $this->bSkipNS      = false;
        /*
            Load main settings.
        */
        $this->load();
    }

    /**
     * Overload magic property setter function.
     *
     * @param   string  $sName      The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value
     */
    protected function __set ( $sName, $mValue )
    {
        /*
            Prefix name.
        */
        $sCName = strtoupper($this->sCurrent . '_' . $sName);
        /*
            Let's define as constant if possible, not exists and it's required.
        */
        if (is_scalar($mValue) and !defined($sCName)
            and $this->aToConstants[$this->sCurrent])
        {
            define($sCName, $mValue);
        }
        /*
            Save new value internally.
        */
        $this->aData[$this->sCurrent][$sName] = $mValue;
        /*
            Return constant value.
        */
        return $mValue;
    }

    /**
     * Overload magic property getter function.
     *
     * @param   string  $sName      The name/key string
     *
     * @return  mixed   The key value or null
     */
    protected function __get ( $sName )
    {
        /*
            If unknown key return null.
        */
        if (!isset($this->aData[$this->sCurrent][$sName]))
        {
            return null;
        }
        /*
            Return value.
        */
        return $this->aData[$this->sCurrent][$sName];
    }

    /**
     * Overload magic property check function.
     *
     * @param   string  $sName      The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    protected function __isset ( $sName )
    {
        return isset($this->aData[$this->sCurrent][$sName]);
    }

    /**
     * Overload magic property unsetter function.
     *
     * @param   string  $sName      The name/key string
     */
    protected function __unset ( $sName )
    {
        unset($this->aData[$this->sCurrent][$sName]);
    }

    /**
     * Returns value of $sKey in $sNamespace.
     *
     * @param   string  $sNamespace The namespace
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mDefault   A default if setting not found
     *
     * @return  mixed   The key value, default value or null
     */
    public function get ( $sNamespace, $sKey, $mDefault = null )
    {
        if (isset($this->aData[$sNamespace]))
        {
            if (isset($this->aData[$sNamespace][$sKey]))
            {
                return $this->aData[$sNamespace][$sKey];
            }
        }
        return $mDefault;
    }

    /**
     * Returns entire $sNamespace array.
     *
     * @param   string  $sNamespace The namespace
     *
     * @return  mixed   The key value or null
     */
    public function get_ns ( $sNamespace )
    {
        if (isset($this->aData[$sNamespace]))
        {
            return $this->aData[$sNamespace];
        }
        return null;
    }

    /**
     * Sets value of $sKey in $sNamespace.
     *
     * @param   string  $sNamespace The namespace
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     */
    public function set ( $sNamespace, $sKey, $mValue )
    {
        $sPrevious = $this->sCurrent;
        $this->namespace($sNamespace);
        $this->__set($sKey, $mValue);
        $this->namespace($sPrevious);
    }

    /**
     * Define and|or use a namespace.
     *
     * @param   string  $sNamespace     The namespace
     * @param   boolean $bUseConstants  Create constants of settings
     *
     * @return  object  Returns itself for method chaining
     */
    public function namespace ( $sNamespace = null, $bUseConstants = null )
    {
        /*
            Check if we have to skip this.
        */
        if ($this->bSkipNS)
        {
            return $this;
        }
        /*
            Reset namespace ?
        */
        if (is_null($sNamespace))
        {
            $this->sCurrent = $this->sMaster;
            return $this;
        }
        /*
            Save current namespace.
        */
        $this->sCurrent = $sNamespace;
        /*
            Should we use constants ?
        */
        if (is_bool($bUseConstants))
        {
            $this->aToConstants[$sNamespace] = $bUseConstants;
        }
        elseif (!isset($this->aToConstants[$sNamespace]))
        {
            $this->aToConstants[$sNamespace] = true;
        }
        /*
            Create sub-array if it's not there already.
        */
        if (!isset($this->aData[$sNamespace]))
        {
            $this->aData[$sNamespace] = array();
        }
        return $this;
    }
    /**
     * Alias for namespace.
     *
     * @see namespace()
     */
    public function ns ( $sNamespace = null, $bUseConstants = null )
    {
        return $this->namespace($sNamespace, $bUseConstants);
    }

    /**
     * Load a settings file.
     *
     * @param   string  $sFile      The filename
     * @param   string  $sNamespace The namespace
     *
     * @return  object  Returns itself for method chaining
     */
    public function load ( $sFile = PROJECT_SETTINGS, $sNamespace = null )
    {
        /*
            Save current namespace.
        */
        $sPrevious = $this->sCurrent;
        if (!is_null($sNamespace))
        {
            /*
                Override namespace definitions.
            */
            $this->namespace($sNamespace);
            $this->bSkipNS = true;
        }
        else
        {
            /*
                Switch to master namespace.
            */
            $this->namespace($this->sMaster);
        }
        /*
            Get settings.
        */
        ob_start();
        $bOk = include $sFile;
        ob_end_clean();
        /*
            Select previous namespace.
        */
        $this->sCurrent = $sPrevious;
        $this->bSkipNS = false;
        return $this;
    }

    /**
     * Save a settings file.
     *
     * @param   string  $sFile      The filename
     *
     * @return  mixed   Returns itself for method chaining or the built array
     */
    public function save ( $sFile = false )
    {
        /*
            These are the first lines of the file.
        */
        $aLines  = array("<?php if (!isset(\$this)) { header('HTTP/1.0 404 Not Found'); die(); }\
                            \n/*\n\tSettings file generated by Logicoder Web Application Framework.\
                            \n\n\tCreated on ".date(DATE_RSS)."\n*/");
        $aLinLen = array(0, 0);
        $nMaxLen = 0;
        $t = 1;
        /*
            Prepares all the namespaces.
        */
        foreach ($this->aData as $sNamespace => $aSettings)
        {
            if (count($aSettings) == 0)
            {
                continue;
            }
            else
            {
                $aLines[] = "\n// -----------------------------------------------------------------------------\n";
                $aLines[] = '$this->namespace("' . $sNamespace . '", '
                            . var_export($this->aToConstants[$sNamespace], true) . ');' . "\n";
                $t += 2;
            }
            /*
                Prepare the labels using the prefix as array name.
            */
            foreach (array_keys($aSettings) as $sKey)
            {
                $tLen = $aLinLen[] = strlen($aLines[] = "\$this[\"$sKey\"] ");
                $nMaxLen = ($tLen > $nMaxLen) ? $tLen : $nMaxLen;
            }
        }
        /*
            Export values padding the labels.
        */
        $t = 3;
        foreach ($this->aData as $sNamespace => $aSettings)
        {
            if (count($aSettings) == 0)
            {
                continue;
            }
            foreach (array_values($aSettings) as $mValue)
            {
                $aLines[$t] = str_pad($aLines[$t], $nMaxLen) . '= ' . str_replace("'", '"', var_export($mValue, true)) . ';';
                ++$t;
            }
            $t += 2;
        }
        $aLines[] = "\n// -----------------------------------------------------------------------------\n";
        /*
            If no filename given, return the built array.
        */
        if ( $sFile == false )
        {
            return $aLines;
        }
        elseif (file_put_contents($sFile, join($aLines,"\n"), LOCK_EX) === 0)
        {
            throw new Exception("Can't save settings file '$sFile'.");
        }
        return $this;
    }
}
// END Logicoder_Settings class

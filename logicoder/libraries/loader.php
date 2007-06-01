<?php
/**
 * Logicoder Web Application Framework - Loader library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * The Loader class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/loading.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Loader
{
    /**
     * The loaded files array.
     */
    protected $aLoaded = array();

    /**
     * Constructor.
     */
    public function __construct ( /* void */ )
    {
        /*
            Register our autoload method.
        */
        spl_autoload_register(array($this, '__autoload'));
    }

    /**
     * Autoloader.
     */
    public function __autoload ( $sClassName )
    {
        /*
            Auto-detect application models.
        */
        if (strpos($sClassName, 'Logicoder_Model') === false
            and strpos($sClassName, '_Model') !== false)
        {
            return $this->model(str_replace('_Model', '', $sClassName));
        }
        /*
            Should be a library.

            - Remove 'Logicoder_' prefix to allow for library overloading.
        */
        return $this->library(str_replace('Logicoder_', '', $sClassName));
    }

    /**
     * Real loader method that looks for overloaded version of a file.
     *
     * @param   string  $sName      Component to load
     * @param   string  $sPath      Path to haystack directory
     * @param   string  $sFile      Override filename algorithm
     * @param   string  $sClass     Override classname algorithm
     * @param   boolean $bClsCheck  Re-check for class once loaded
     *
     * @return  string  Class name
     */
    protected function __load ( $sName, $sPath = '', $sFile = false, $sClass = false, $bClsCheck = false )
    {
        /*
            If class is already loaded, return it.
        */
        if (isset($this->aLoaded[$sName]))
        {
            return $this->aLoaded[$sName];
        }
        elseif (($sClass !== false) and class_exists($sClass, false))
        {
            return $sClass;
        }
        /*
            Prepare filename.
        */
        if ($sFile === false)
        {
            /*
                Get filename converting class|module name.
            */
            $sFile = str_replace('_', '/', strtolower($sName));
        }
        $sFile .= EXT;

        /*
            Search in project, apps, core and directly.
        */
        if (is_readable(APPS_ROOT . $sPath . $sFile))
        {
            /*
                Found an overloader in project/path.
            */
            $sFile = APPS_ROOT . $sPath . $sFile;
            /*
                Set the class if not passed.
            */
            $sClass = ($sClass) ? $sClass : $sName;
        }
        elseif (defined('APP_PATH') and is_readable(APPS_ROOT . APP_PATH . $sPath . $sFile))
        {
            /*
                Found an overloader in project/path.
            */
            $sFile = APPS_ROOT . APP_PATH . $sPath . $sFile;
            /*
                Set the class if not passed.
            */
            $sClass = ($sClass) ? $sClass : $sName;
        }
        elseif (is_readable(LOGICODER_ROOT . $sPath . $sFile))
        {
            /*
                Found in the core distribution.
            */
            $sFile = LOGICODER_ROOT . $sPath . $sFile;
            /*
                Prefix the name to get the class if not passed.
            */
            $sClass = ($sClass) ? $sClass : 'Logicoder_'.$sName;
        }
        elseif (!is_readable($sPath . $sFile))
        {
            throw new Exception("Can't find class file for '$sName' which I searched in '$sPath$sFile'.", 404);
        }
        /*
            Load the class/module file.
        */
        ob_start();
        $bOk = include_once $sFile;
        ob_end_clean();
        if (!$bOk)
        {
            throw new Exception("Can't load file '$sFile' for '$sName'.");
        }
        $this->aLoaded[$sName] = $sClass;
        /*
            Check if class now exists.
        */
        if ($bClsCheck and !class_exists($sClass))
        {
            throw new Exception("File '$sFile' loaded but class '$sClass' doesn't exist !?");
        }
        /*
            Return class name.
        */
        return $sClass;
    }

    /**
     * Loads a library, calls a factory method to return a new instance.
     *
     * @param   string  $sLibrary       Library name
     * @param   boolean $bNewInstance   True to return a new object of the load library
     * @param   array   $aParams        Parameters to pass to the object constructor
     *
     * @return  mixed   Class name or object instance
     */
    public function library ( $sName, $bNewInstance = false, $aParams = array() )
    {
        /*
            Loads the library.
        */
        $sClass = $this->__load($sName, 'libraries/', false, false, true);
        /*
            Create a new instance ?
        */
        if ($bNewInstance)
        {
            if (method_exists($sClass, 'instance'))
            {
                /*
                    Use factory|singleton method.
                */
                return call_user_func_array(array($sClass, 'instance'), $aParams);
            }
            else
            {
                /*
                    Use standard "new" method.
                */
                return call_user_func_array(array(new ReflectionClass($sClass), 'newInstance'), $aParams);
            }
        }
        return $sClass;
    }

    /**
     * Load helper(s).
     *
     * @param   mixed   $mName      String or array of helper names
     *
     * @return  mixed   String or array of loaded helpers
     */
    public function helper ( $mName )
    {
        if (is_array($mName))
        {
            $aRet = array();
            /*
                Recursive call loop.
            */
            foreach ($mName as $sName)
            {
                $aRet[] = $this->helper($sName);
            }
            return $aRet;
        }
        else
        {
            /*
                Build class from name.
            */
            $sClass = $mName . '_Helper';
            /*
                Load the helper file.
            */
            return $this->__load($sClass, 'helpers/', strtolower($mName), $sClass);
        }
    }

    /**
     * Loads a model.
     *
     * @param   string  $sName      Model name
     * @param   string  $sPath      Path to haystack directory
     * @param   string  $sClass     Override classname algorithm
     *
     * @return  string  Loaded class name
     */
    public function model ( $sName, $sPath = 'models/', $sClass = false )
    {
        /*
            Build class from name if not passed.
        */
        $sClass = ($sClass === false) ? ucfirst($sName) . '_Model' : $sClass;
        /*
            Try to load the model.
        */
        try
        {
            $sClass = $this->__load($sClass, $sPath, strtolower($sName), $sClass);
        }
        catch (Exception $e)
        {
            if ($e->getCode() !== 404)
            {
                throw $e;
            }
            /*
                Try to search for models file in application path.
            */
            return $this->__load($sClass, '', 'models', $sClass, true);
        }
        return $sClass;
    }

    /**
     * Load views.
     *
     * @param   string  $sName      View name or null to auto-populate
     * @param   array   $aData      Data to populate view
     * @param   integer $nType      Override view detection algorithm
     *
     * @return  object  Loaded class instance
     */
    public function view ( $sName = null, array $aData = null, $nType = null )
    {
        /*
            Load the View Factory.
        */
        $sFactory = $this->library('View_Factory');
        /*
            Automate view name.
        */
        if (is_null($sName))
        {
            $aBT = debug_backtrace();
            $sName = $aBT[1]['function'];
        }
        /*
            Call the factory for an instance.
        */
        $oView = call_user_func(array($sFactory, 'instance'), $sName, $nType);
        /*
            If data is passed, parse the view.
        */
        if (!is_null($aData))
        {
            $oView->render($aData);
        }
        /*
            Return the view object.
        */
        return $oView;
    }

    /**
     * Loads a controller.
     *
     * @param   string  $sName          Controller name
     * @param   string  $sPath          Path to haystack directory
     * @param   string  $sClass         Override classname algorithm
     * @param   boolean $bNewInstance   True to return a new object of the load library
     *
     * @return  mixed   Class name or object instance
     */
    public function controller ( $sName, $sPath = 'controllers/', $sClass = false, $bNewInstance = true )
    {
        /*
            Build class from name if not passed.
        */
        $sClass = ($sClass === false) ? ucfirst($sName) . '_Controller' : $sClass;
        /*
            Load the controller class file.
        */
        $this->__load($sClass, $sPath, strtolower($sName), $sClass);
        /*
            Create the controller instance, if required.
        */
        if ($bNewInstance)
        {
            return call_user_func_array(array(new ReflectionClass($sClass), 'newInstance'), null);
        }
    }
}
// END LogiCoder_Loader class

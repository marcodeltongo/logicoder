<?php
/**
 * Logicoder Web Application Framework - Router library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Routing file path.
 */
if (!defined('ROUTES_PATH'))
{
    define('ROUTES_PATH', APPS_ROOT);
}

// -----------------------------------------------------------------------------

/**
 * Router class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/router.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Router
{
    /**
     * The original URI.
     */
    protected $sURI;

    /**
     * The request method.
     */
    protected $sMethod;

    /**
     * The routes mappings.
     */
    protected $aRoutes;

    /**
     * The routed URI.
     */
    protected $sRouted;

    /**
     * The URI segments.
     */
    protected $aSegments;

    /**
     * The controller path.
     */
    protected $sControllerPath;

    /**
     * The controller filename.
     */
    protected $sControllerFile;

    /**
     * The controller classname.
     */
    protected $sControllerClass;

    /**
     * The controller method.
     */
    protected $sControllerAction;

    /**
     * The controller data.
     */
    protected $aControllerData;

    // -------------------------------------------------------------------------

    /**
     * Overload magic property setter function.
     *
     * @param   string  $sName      The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value or null
     */
    protected function __set ( $sName, $mValue )
    {
        switch ($sName)
        {
            case 'url':
                return $this->sRouted           = $mValue;
            break;
            case 'http_method':
                return $this->sMethod           = $mValue;
            break;
            case 'path':
                return $this->sControllerPath   = $mValue;
            break;
            case 'filename':
                return $this->sControllerFile   = $mValue;
            break;
            case 'classname':
                return $this->sControllerClass  = $mValue;
            break;
            case 'action':
                return $this->sControllerAction = $mValue;
            break;
            case 'params':
                return $this->aControllerData   = (array)$mValue;
            break;
            default:
                return null;
        }
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
        switch ($sName)
        {
            case 'url':
                return $this->sRouted;
            break;
            case 'http_method':
                return $this->sMethod;
            break;
            case 'path':
                return $this->sControllerPath;
            break;
            case 'filename':
                return $this->sControllerFile;
            break;
            case 'classname':
                return $this->sControllerClass;
            break;
            case 'action':
                return $this->sControllerAction;
            break;
            case 'params':
                return (array)$this->aControllerData;
            break;
            default:
                return null;
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Route the request.
     *
     * @param   string  $sURI       The original URI
     * @param   array   $aRoutes    The routes mappings
     */
    public function route ( $sURI = null, array $aRoutes = null )
    {
        /*
            Get the URI.
        */
        if (!is_string($sURI))
        {
            /*
                Build the URI.
            */
            $this->sURI = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '';
            /*
                Add query string if required.
            */
            if (ROUTER_QUERY_STRING)
            {
                $this->sURI .= (isset($_SERVER['QUERY_STRING'])) ? '/'.strtr($_SERVER['QUERY_STRING'], '=&','//') : '';
            }
            /*
                Clean URI.
            */
            $this->sURI = trim($this->sURI, '/');
        }
        else
        {
            /*
                Passed as parameter.
            */
            $this->sURI = $sURI;
        }
        /*
            How was the request made ?
        */
        $this->sMethod = $_SERVER['REQUEST_METHOD'];
        /*
            Initialize segments.
        */
        $this->aSegments = array();
        /*
            Route mappings from parameter.
        */
        if (!is_null($aRoutes))
        {
            $this->aRoutes = $aRoutes;
        }
        /*
            Route matching.
        */
        if ($this->sURI == '')
        {
            /*
                No URI, follow the default route.
            */
            $this->aSegments = explode('/', $this->aRoutes['default_controller']);
            $this->sRouted = $this->aRoutes['default_controller'];
        }
        elseif (array_key_exists($this->sURI, $this->aRoutes))
        {
            /*
                Exact key match check, replace the segments.
            */
            $this->aSegments = explode('/', $this->aRoutes[$this->sURI]);
            $this->sRouted = $this->aRoutes[$this->sURI];
        }
        else
        {
            /*
                Check for any matching regex route.
            */
            $this->match($this->aRoutes);
        }
        /*
            No one found yet ?
        */
        if (empty($this->aSegments))
        {
            /*
                If so simply split the URI
            */
            $this->aSegments = explode('/', $this->sURI);
            $this->sRouted = $this->sURI;
        }
        /*
            Ok, let's go mapping
        */
        $this->map();
    }

    /**
     * Load routes from files.
     *
     * @param   string  $sFile      File containing routes
     * @param   mixed   $mKey       Key for sub routes
     *
     * @return  array   Main or sub routes
     */
    public function load ( $sFile, $mKey = false )
    {
        /*
            Get URI routes.
        */
        ob_start();
        $bOk = require(ROUTES_PATH . $sFile);
        ob_end_clean();
        /*
            Merge URI routes.
        */
        if ($bOk and isset($urls) and is_array($urls))
        {
            if ($mKey === false)
            {
                /*
                    Main (Project) URI routes.
                */
                $this->aRoutes = array_merge_recursive((array)$this->aRoutes, $urls);
            }
            else
            {
                /*
                    Sub (Apps) URI routes.
                */
                $aSubRoutes = array();
                $sSubPath   = substr($sFile, 0, -strlen(ROUTER_FILE));
                foreach($urls as $mPattern => $mRoute)
                {
                    $aSubRoutes[$mKey . $mPattern] = $sSubPath . $mRoute;
                }
                return $this->aRoutes[$mKey] = $aSubRoutes;
            }
        }
        return $this->aRoutes;
    }

    /**
     * Search matching routes.
     *
     * @param   array   $aRoutes    Array of routes to match to
     */
    protected function match ( array $aRoutes )
    {
        foreach ($aRoutes as $mPattern => $mRoute)
        {
            /*
                Check for sub-routes array.
            */
            if (strpos($mRoute, ROUTER_FILE) !== false)
            {
                $this->match($this->load($mRoute, $mPattern));
                continue;
            }
            /*
                Is this a route with wildcards ?
            */
            if (strpos($mPattern,':') !== false)
            {
                /*
                    Convert routes with wildcards to RegEx
                */
                $mPattern = str_replace(array(':any',':num'), array('(\w*)','(\d*)'), $mPattern);
            }
            /*
                Add backreferences if not there already.
            */
            $t = substr_count($mPattern, '(');
            for ($i = 1; $i <= $t; ++$i)
            {
                if (strpos($mRoute, '$' . $i) === false)
                {
                    $mRoute .= '/$' . $i;
                }
            }
            /*
                Replace as per regular expression.
            */
            $mRoutedUri = preg_replace("|^$mPattern$|", $mRoute, $this->sURI);
            /*
                Replace the segments.
            */
            if ($mRoutedUri !== $this->sURI)
            {
                $this->aSegments = explode('/', $mRoutedUri);
                $this->sRouted = $mRoutedUri;
                return;
            }
        }
    }

    /**
     * Try to find a suitable controller.
     *
     * @param   string  $sDir       Root directory to scan
     * @param   integer $iD         Index reference to segments
     *
     * @return  boolean True on success or false on failure
     */
    protected function _file_mapper ( $sDir, $iD = 0 )
    {
        /*
            Recurse dirs.
        */
        while (is_dir(PROJECT_ROOT . $sDir . $this->aSegments[$iD]))
        {
            $sDir .= $this->aSegments[$iD++] . '/';
        }
        /*
            Check for specific controller file.
        */
        if (is_readable(PROJECT_ROOT . $sDir . $this->aSegments[$iD] . EXT))
        {
            $this->sControllerPath  = $sDir;
            $this->sControllerFile  = $this->aSegments[$iD];
            $this->sControllerClass = ucfirst($this->aSegments[$iD]) . '_Controller';
            $this->aControllerData  = array_slice($this->aSegments, ++$iD);
            return true;
        }
        return false;
    }

    /**
     * Map the segments to file system and class
     */
    protected function map ( /* void */ )
    {
        $this->sControllerPath  = '';
        $this->sControllerFile  = '';
        $this->sControllerClass = '';
        $this->sControllerAction= '';
        $this->aControllerData  = array();
        $bFound = false;
        /*
            Start the search with project overloaded controllers.
        */
        if (is_dir(APPS_ROOT . 'controllers/'))
        {
            /*
                Search for project controllers.
            */
            $bFound = $this->_file_mapper('controllers/');
        }
        /*
            Get installed applications.
        */
        $aApps = (defined('LOGICODER')) ? Logicoder::instance()->settings->INSTALLED_APPS : array();
        /*
            Search in applications controllers.
        */
        if (!$bFound and isset($aApps[$this->aSegments[0]]))
        {
            /*
                Get application directory.
            */
            $sAppPath = $aApps[$this->aSegments[0]];

            if (is_dir(APPS_ROOT . $sAppPath . '/controllers/'))
            {
                /*
                    Check in controllers directory in application path.
                */
                $bFound = $this->_file_mapper($sAppPath . '/controllers/', 1);
            }
            elseif (is_readable(APPS_ROOT . $sAppPath . '/controllers' . EXT))
            {
                /*
                    Use controllers file in application path.
                */
                $this->sControllerPath  = $sAppPath . '/';
                $this->sControllerFile  = 'controllers';
                $this->sControllerClass = ucfirst($this->aSegments[0]) . '_Controllers';
                $this->aControllerData  = array_slice($this->aSegments, 1);
                $bFound = true;
            }
            /*
                Save application information.
            */
            if ($bFound)
            {
                /*
                    Save in application object.
                */
                Logicoder::instance()->app->name = $this->aSegments[0];
                Logicoder::instance()->app->path = $sAppPath . '/';
            }
        }
        /*
            Have we found a suitable controller ?
        */
        if (!$bFound)
        {
            throw new Logicoder_404('Controller not found.');
        }
        /*
            Try to load the controller class definition.
        */
        Logicoder::instance()->load->controller($this->sControllerFile, $this->sControllerPath, $this->sControllerClass, false);
        /*
            Map action to named or index.
            The in-array is needed since method_exists returns also non-public methods !
        */
        if (isset($this->aControllerData[0]) and
            in_array($this->aControllerData[0], get_class_methods($this->sControllerClass)))
        {
            $this->sControllerAction = array_shift($this->aControllerData);
        }
        elseif (method_exists($this->sControllerClass, 'index'))
        {
            $this->sControllerAction = 'index';
        }
        else
        {
            throw new Logicoder_404('Controller method not found.');
        }
    }
}
// END Logicoder_Router class

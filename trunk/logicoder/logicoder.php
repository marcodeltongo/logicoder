<?php
/**
 * Logicoder Web Application Framework - Front Controller aka "THE CORE"
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**#@+
 * Required dependencies.
 */
require_once LOGICODER_ROOT . 'libraries/interfaces' . EXT;
require_once LOGICODER_ROOT . 'libraries/exceptions' . EXT;
require_once LOGICODER_ROOT . 'libraries/constants' . EXT;
require_once LOGICODER_ROOT . 'libraries/overarray' . EXT;
require_once LOGICODER_ROOT . 'libraries/registry' . EXT;
require_once LOGICODER_ROOT . 'libraries/loader' . EXT;
/**#@-*/

// -----------------------------------------------------------------------------

/**
 * The Front Controller aka "THE CORE"
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/index.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder extends Logicoder_ObjectRegistry implements Logicoder_iSingleton
{
    /**
     * The singleton instance.
     */
    private static $oInstance = null;

    /**
     *  Returns the singleton instance of the class.
     *
     *  @return object  The singleton instance
     */
    public static function instance ( /* void */ )
    {
        if (self::$oInstance === null)
        {
            self::$oInstance = new Logicoder();
            self::$oInstance->__setup();
        }
        return self::$oInstance;
    }

    /**
     * Overloaded magic function, returns object if called as method.
     *
     * @param   string  $sObject    Object name in the registry
     * @param   array   $aParams    Array of parameters or method + parameters
     *
     * @return  mixed   Null on failure, object or method result on success
     */
    public function __call ( $sObject, $aParams )
    {
        $oReturn = null;
        /*
            Check if it's in the registry.
        */
        if ($this->has($sObject))
        {
            /*
                Get instance.
            */
            $oReturn = $this->get($sObject);
            /*
                Is the first parameter a callable method ?
            */
            if (isset($aParams[0]) and method_exists($oReturn, $aParams[0]))
            {
                /*
                    Call $sObject->$aParams[0]($aParams[...]).
                */
                $sMethod = array_shift($aParams);
                return call_user_func_array(array($oReturn, $sMethod), $aParams);
            }
        }
        return $oReturn;
    }

    /**
     * The *real* constructor, we need this to avoid $this circular reference.
     */
    private function __setup ( /* void */ )
    {
        /*
            Get the loader.
        */
        $this->register('load', new Logicoder_Loader());
        /*
            Load some useful helpers.
        */
        $this->load->helper(array('Array', 'String', 'Html', 'File', 'Debug'));
        /*
            Setup error and exception handler.
        */
        #$this->register('bsod', $this->load->library('BSOD', true));
        /*
            Setup settings manager and load project settings.
        */
        $this->register('settings', $this->load->library('Settings', true));
        $this->settings->load(APPS_ROOT . 'settings' . EXT);
        /*
            Setup logging facility.
        */
        $this->register('logger', $this->load->library('Logger_Factory', true));
        /*
            Setup and start benchmark.
        */
        $this->register('benchmark', $this->load->library('Benchmark', true, (array)BENCHMARK_STARTUP));
        /*
            Setup request object.
        */
        $this->register('request', $this->load->library('Request', true));
        /*
            Setup response object.
        */
        $this->register('response', $this->load->library('Response', true));
        /*
            Setup application object.
        */
        $this->register('app', $this->load->library('Application', true));
        /*
            Setup router object.
        */
        $this->register('router', $this->load->library('Router', true));
        /*
            Load db driver.
        */
        if (DB_AUTOCONNECT)
        {
            $this->register('db', $this->load->library('DB_Factory', true));
        }
        /*
            Try to get the controller up and running.
        */
        try
        {
            /*
                Let the router find the way...
            */
            $this->router->route();
            /*
                We should have a valid controller, now.
            */
            $this->register('controller', new $this->router->classname($this));
            /*
                And finally, let's get some action.
            */
            return call_user_func_array(array($this->controller, $this->router->action), $this->router->params);
        }
        catch (Logicoder_404 $e)
        {
            if ($e->getCode() == 404)
            {
                /*
                    Serve error via Response.
                */
                $this->response->not_found($e->getMessage());
            }
        }
    }
}
// END Logicoder class

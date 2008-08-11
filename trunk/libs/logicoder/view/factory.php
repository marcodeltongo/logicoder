<?php
/**
 * Logicoder Web Application Framework - View factory
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * View factory class.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View_Factory implements Logicoder_iFactory
{
    /**
     * Returns a new instance of a View class.
     *
     * @param   string  $sViewSrc   The view name / query / method / source
     * @param   numeric $nType      The view type
     * @param   string  $sAppDir    The application sub-directory
     * @param   string  $sAppName   The application name
     *
     * @return  object  Returns a view instance or throws a 404 exception
     */
    public static function instance ( $sViewSrc = '?', $nType = null, $sAppDir = APP_PATH, $sAppName = APP_NAME )
    {
        /*
            If a specific type is requested, simply try to load it.
        */
        if (!is_null($nType))
        {
            return self::load($nType, $sViewSrc, $sAppDir, $sAppName);
        }
        /*
            Start the search with project overloaded views.
        */
        $sSearch = APPS_ROOT . 'views/' . $sAppDir;
        if (is_dir($sSearch) and $mFound = self::map($sSearch . $sViewSrc))
        {
            return $mFound;
        }
        /*
            Search for standard application views.
        */
        if ($mFound = self::map(APPS_ROOT . $sAppDir . 'views/' . $sViewSrc))
        {
            return $mFound;
        }
        /*
            At last, try with a 'views.php' in the application directory.
        */
        if (is_readable(APPS_ROOT . $sAppDir . 'views' . EXT))
        {
            return self::load(PROXY_VIEW, $sViewSrc, $sAppDir, $sAppName);
        }
        /*
            Whoops !
        */
        throw new Logicoder_404("View '$sViewSrc' for '$sAppName' application not found.");
    }

    /**
     * Load and instantiate a view.
     *
     * @param   numeric $nType      The view type
     * @param   string  $sViewSrc   The view pathname / query / source
     * @param   string  $sAppDir    The application sub-directory
     * @param   string  $sAppName   The application name
     *
     * @return  object  Returns a view instance or throws a 404 exception
     */
    protected static function load ( $nType, $sViewSrc, $sAppDir = APP_PATH, $sAppName = APP_NAME )
    {
        /*
            Proxy views.
        */
        if ($nType & VIEW_IS_CLASS)
        {
            $sClass = $sAppName . '_Views';
            /*
                Load the application views class definition.
            */
            if (!class_exists($sClass, false))
            {
                ob_start();
                include(APPS_ROOT . $sAppDir . 'views' . EXT);
                ob_end_clean();
            }
            $oView = new $sClass();
            /*
                Set method to call and return instance.
            */
            return $oView->method($sViewSrc);
        }
        /*
            Standard views.
        */
        if ($nType & VIEW_IS_PHP)
        {
            $sClass = 'View_PHP';
        }
        elseif ($nType & VIEW_IS_HTML)
        {
            $sClass = 'View_HTML';
        }
        elseif ($nType & VIEW_IS_TEMPLATE)
        {
            $sClass = 'View_Template';
        }
        elseif ($nType & VIEW_IS_DWT)
        {
            $sClass = 'View_DWT';
        }
        else
        {
            throw new Logicoder_404('View parser unknown.');
        }
        /*
            Load the view implementation.
        */
        $oView = new $sClass();
        /*
            Where to get view from ?
        */
        if ($nType & VIEW_FROM_FILE)
        {
            /*
                Load it from a file.
            */
            $oView->load($sViewSrc);
        }
        elseif ($nType & VIEW_FROM_QUERY)
        {
            /*
                Get it from a db.
            */
            $oView->query($sViewSrc);
        }
        elseif ($nType & VIEW_FROM_PARAM)
        {
            /*
                We already have it !
            */
            $oView->source($sViewSrc);
        }
        else
        {
            throw new Logicoder_404('View source type unknown.');
        }
        /*
            Return instance.
        */
        return $oView;
    }

    /**
     * Tries to map view name to a file in the system.
     * NOTE: since uses glob() there may be problems on some platforms.
     *
     * @param   string  $sViewSrc   The view name / query / source
     *
     * @return  mixed   A view instance or boolean false
     */
    protected static function map ( $sViewSrc )
    {
        /*
            There is a flat html view file ?
        */
        if ($mFound = glob($sViewSrc . HTML_VIEW_MASK, GLOB_BRACE))
        {
            return self::load(HTML_VIEW, $mFound[0]);
        }
        /*
            There is a php view file ?
        */
        if ($mFound = glob($sViewSrc . PHP_VIEW_MASK, GLOB_BRACE))
        {
            return self::load(PHP_VIEW, $mFound[0]);
        }
        /*
            There is a templated view file ?
        */
        if ($mFound = glob($sViewSrc . TEMPLATE_VIEW_MASK, GLOB_BRACE))
        {
            return self::load(TEMPLATE_VIEW, $mFound[0]);
        }
        /*
            None found.
        */
        return false;
    }
}
// END Logicoder_View_Factory class

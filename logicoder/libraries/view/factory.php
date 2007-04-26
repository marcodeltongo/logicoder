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
     * @param   string  $sViewSrc   The view name / query / source
     * @param   numeric $nType      The view type
     *
     * @return  object  Returns a view instance or throws a 404 exception
     */
    public static function instance ( $sViewSrc = null, $nType = null )
    {
        /*
            If a specific type is requested, simply try to load it.
        */
        if (!is_null($nType))
        {
            return self::_load($nType, $sViewSrc);
        }
        /*
            Alias to main app.
        */
        $oApp = Logicoder::instance()->app;
        /*
            Start the search with project views.
        */
        if (is_dir(PROJECT_ROOT . 'views/') and
            $mFound = self::_map(PROJECT_ROOT . 'views/' . $oApp->name . $sViewSrc))
        {
            return $mFound;
        }
        /*
            Search for application views.
        */
        if ($mFound = self::_map(PROJECT_ROOT . $oApp->path . 'views/' . $sViewSrc))
        {
            return $mFound;
        }
        /*
            At last, try with a 'views.php' in the application directory.
        */
        if (is_readable(PROJECT_ROOT . $oApp->path . 'views' . EXT))
        {
            /*
                Load the application views class definition.
            */
            $sClass = $oApp->name . '_Views';
            Logicoder()->load->__load($sClass, $oApp->path, 'views', $sClass, true);
            /*
                If we found a suitable view, return a new instance of it.
            */
            if (method_exists($sClass, $sViewSrc))
            {
                /*
                    Get a new instance, set method and return it.
                */
                $oView = new $sClass();
                return $oView->method($sViewSrc);
            }
        }
        /*
            Whoops !
        */
        throw new Logicoder_404('View not found.');
    }

    /**
     * Load and instantiate a view.
     *
     * @param   numeric $nType      The view type
     * @param   string  $sViewSrc   The view name / query / source
     *
     * @return  object  Returns a view instance or throws a 404 exception
     */
    protected static function _load ( $nType, $sViewSrc )
    {
        /*
            Parser to use.
        */
        $sEngine = '';
        if ($nType & VIEW_IS_HTML)
        {
            $sEngine = 'HTML';
        }
        elseif ($nType & VIEW_IS_PHP)
        {
            $sEngine = 'PHP';
        }
        elseif ($nType & VIEW_IS_TEMPLATE)
        {
            $sEngine = 'Template';
        }
        else
        {
            throw new Logicoder_404('View parser unknown.');
        }
        /*
            Load the view implementation.
        */
        $sClass = Logicoder()->load->__load('View_' . $sEngine, 'libraries/');
        /*
            Get a new instance.
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
    protected static function _map ( $sViewSrc )
    {
        /*
            There is a flat html view file ?
        */
        if ($mFound = glob($sViewSrc . HTML_VIEW_MASK, GLOB_BRACE))
        {
            return self::_load(HTML_VIEW, $mFound[0]);
        }
        /*
            There is a php view file ?
        */
        if ($mFound = glob($sViewSrc . PHP_VIEW_MASK, GLOB_BRACE))
        {
            return self::_load(PHP_VIEW, $mFound[0]);
        }
        /*
            There is a templated view file ?
        */
        if ($mFound = glob($sViewSrc . TEMPLATE_VIEW_MASK, GLOB_BRACE))
        {
            return self::_load(TEMPLATE_VIEW, $mFound[0]);
        }
        /*
            None found.
        */
        return false;
    }
}
// END Logicoder_View_Factory class

<?php
/**
 * Logicoder Web Application Framework - HTML Helpers
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/debug.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
define('HTML_HELPER', true);

/**#@+
 * Required dependency.
 */
if (!defined('ARRAY_HELPER'))
{
    if (defined('LOGICODER'))
    {
        Logicoder::instance()->load->helper('Array');
    }
    else
    {
        require('array.php');
    }
}
/**#@-*/

// -----------------------------------------------------------------------------

/**
 * Buils a tag.
 *
 * @param   string  $sTag       The tag ;)
 * @param   string  $sCData     The contents
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 * @param   array   $aAttrs     Array of attributes
 * @param   array   $aOn        Array of On* event handlers
 *
 * @return  string  HTML snippet string
 */
function _tag ( $sTag, $sCData = null,
                $sId = null, $mCls = null,
                array $aAttrs = null, array $aOn = null )
{
    $sAdditional = '';
    /*
        Self-closing or coupled ?
    */
    $bCouple = !in_array($sTag, array('br', 'hr', 'meta', 'link','input','img'));
    /*
        Prepare data.
    */
    $sCData = (is_null($sCData)) ? '' : $sCData;
    /*
        Prepare ID.
    */
    $sId = (is_null($sId)) ? '' : " id='$sId'";
    /*
        Prepare Class(es).
    */
    $mCls = (is_null($mCls)) ? '' :
            ((is_array($mCls)) ? " class='" . implode($mCls, ' ') . "'" : " class='$mCls'");
    /*
        Prepare attributes.
    */
    if (!is_null($aAttrs))
    {
        foreach ($aAttrs as $attr => $val)
        {
            $sAdditional = " $attr='" . addslashes($val) . "'";
        }
    }
    /*
        Prepare events.
    */
    if (!is_null($aOn))
    {
        foreach ($aOn as $event => $code)
        {
            $sAdditional = " on$event='" . addslashes($code) . "'";
        }
    }
    /*
        Return string.
    */
    return ($bCouple) ? '<'.$sTag.$sId.$mCls.$sAdditional.'>'.$sCData.'</'.$sTag.'>'
                        : '<'.$sTag.$sId.$mCls.$sAdditional.' />';
}
// END _tag function

// -----------------------------------------------------------------------------

/**
 * Builds a list from an array.
 *
 * @param   string  $sTag       Tag to use
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function array2list ( $sTag, array $aData, $sId = null, $mCls = null )
{
    if (is_null($aData) or empty($aData))
    {
        return '';
    }
    /*
        Indentation level.
    */
    static $tabLevel = 0;
    ++$tabLevel;
    /*
        Loop over items.
    */
    $html = "\n";
    foreach ($aData as $key => $item)
    {
        /*
            Go in a deeper level if it's an array.
        */
        if (is_array($item))
        {
            /*
                If key is a string, prepare to use it, then build the sub-list.
            */
            $key = (is_int($key)) ? '' : $key . " ";
            $item = $key . array2list($sTag, $item);
        }
        $html .= str_repeat("\t", $tabLevel) . "<li>$item</li>\n";
    }
    --$tabLevel;
    /*
        Prepare id and class(es).
    */
    $sId = (is_null($sId)) ? '' : " id='$sId'";
    $mCls = (is_null($mCls)) ? '' :
            ((is_array($mCls)) ? " class='" . implode($mCls, ' ') . "'" : " class='$mCls'");
    /*
        Build and return.
    */
    return "<$sTag$sId$mCls>" . $html . str_repeat("\t", $tabLevel) . "</$sTag>";
}
// END array2list function

// -----------------------------------------------------------------------------

/**
 * Builds a list of a:hrefs from an array.
 *
 * @param   string  $sTag       Tag to use
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function array2alist ( $sTag, array $aData, $sId = null, $mCls = null )
{
    if (is_null($aData) or empty($aData))
    {
        return '';
    }
    /*
        Indentation level.
    */
    static $tabLevel = 0;
    ++$tabLevel;
    /*
        Loop over items.
    */
    $html = "\n";
    foreach ($aData as $key => $item)
    {
        /*
            Go in a deeper level if it's an array.
        */
        if (is_array($item))
        {
            $item = array2alist($sTag, $item);
            $html .= str_repeat("\t", $tabLevel) . "<li>$key $item</li>\n";
        }
        elseif (is_int($key))
        {
            $html .= str_repeat("\t", $tabLevel) . "<li>$item</li>\n";
        }
        else
        {
            $html .= str_repeat("\t", $tabLevel) . "<li><a href='$item'>$key</a></li>\n";
        }
    }
    --$tabLevel;
    /*
        Prepare id and class(es).
    */
    $sId = (is_null($sId)) ? '' : " id='$sId'";
    $mCls = (is_null($mCls)) ? '' :
            ((is_array($mCls)) ? " class='" . implode($mCls, ' ') . "'" : " class='$mCls'");
    /*
        Build and return.
    */
    return "<$sTag$sId$mCls>" . $html . str_repeat("\t", $tabLevel) . "</$sTag>";
}
// END array2alist function

// -----------------------------------------------------------------------------

/**
 * Builds a unordered list from an array.
 *
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function ul ( array $aData, $sId = null, $mCls = null )
{
    return array2list('ul', $aData, $sId, $mCls);
}
// END ul function

// -----------------------------------------------------------------------------

/**
 * Builds an ordered list from an array.
 *
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function ol ( array $aData, $sId = null, $mCls = null )
{
    return array2list('ol', $aData, $sId, $mCls);
}
// END ol function

// -----------------------------------------------------------------------------

/**
 * Builds a unordered list from an array.
 *
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function ul_a ( array $aData, $sId = null, $mCls = null )
{
    return array2alist('ul', $aData, $sId, $mCls);
}
// END ul_a function

// -----------------------------------------------------------------------------

/**
 * Builds an ordered list from an array.
 *
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function ol_a ( array $aData, $sId = null, $mCls = null )
{
    return array2alist('ol', $aData, $sId, $mCls);
}
// END ol_a function

// -----------------------------------------------------------------------------

/**
 * Builds a table row.
 *
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 * @param   string  $sCellTag   Tag for cells
 *
 * @return  string  HTML string
 */
function table_row ( array $aData, $sId = null, $mCls = null, $sCellTag = 'td' )
{
    $html = '';
    foreach ($aData as $item)
    {
        $html .= "<$sCellTag>$item</$sCellTag>";
    }
    return _tag('tr', $html, $sId, $mCls) . "\n";
}
// END table_row function

// -----------------------------------------------------------------------------

/**
 * Builds a table rowset.
 *
 * @param   array   $aData      Source data array
 * @param   mixed   $mCls       String or array of classes
 * @param   string  $sCellTag   Tag for cells
 *
 * @return  string  HTML string
 */
function table_rows ( array $aData, $mCls = null, $sCellTag = 'td' )
{
    $html = '';
    foreach ($aData as $item)
    {
        $html .= table_row($item, null, $mCls, $sCellTag);
    }
    return $html;
}
// END table_row function

// -----------------------------------------------------------------------------

/**
 * Builds an html table.
 *
 * @param   string  $sCaption   Caption
 * @param   array   $aHead      Header data array
 * @param   array   $aBody      Body data array
 * @param   array   $aFoot      Footer data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 *
 * @return  string  HTML string
 */
function table ( $sCaption = null, array $aHead = null, array $aBody = null,
                    array $aFoot = null, $sId = null, $mCls = null )
{
    /*
        Prepare id and class(es).
    */
    $sId = (is_null($sId)) ? '' : " id='$sId'";
    $mCls = (is_null($mCls)) ? '' :
            ((is_array($mCls)) ? " class='" . implode($mCls, ' ') . "'" : " class='$mCls'");
    /*
        Start table.
    */
    $html = "<table$sId$mCls>\n";
    if (!is_null($sCaption))
    {
        $html .= "<caption>$sCaption</caption>\n";
    }
    /*
        Build head, body and foot.
    */
    if (!is_null($aHead))
    {
        $html .= "<thead>\n" . table_rows($aHead, null, 'th') . "</thead>\n";
    }
    if (!is_null($aBody))
    {
        $html .= "<tbody>\n" . table_rows($aBody) . "</tbody>\n";
    }
    if (!is_null($aFoot))
    {
        $html .= "<tfoot>\n" . table_rows($aFoot) . "</tfoot>\n";
    }
    /*
        Close table.
    */
    return $html . "</table>\n";
}
// END table function

// -----------------------------------------------------------------------------

/**
 * Builds an html table from an array.
 *
 * @param   array   $aData      Source data array
 * @param   string  $sId        The ID attribute
 * @param   mixed   $mCls       String or array of classes
 * @param   boolean $bHead      Whether the first row is the head
 *
 * @return  string  HTML string
 */
function array2table ( array $aData, $sId = null, $mCls = null, $bHead = false )
{
    /*
        If rows have arrays it's a bit more complex affair.
    */
    if (array_levels($aData) > 2)
    {
        throw new Exception("Function array2table can't handle deep arrays.");
    }
    /*
        Split head if required
    */
    $aHead = ($bHead) ? array(array_shift($aData)) : null;
    /*
        Build the table.
    */
    return table(null, $aHead, $aData, null, $sId, $mCls);
}
// END array2table function

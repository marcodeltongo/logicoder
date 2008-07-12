<?php
/**
 * Logicoder Web Application Framework - SYLK Helper
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/sylk.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
define('SYLK_HELPER', true);

// -----------------------------------------------------------------------------

/**
 * Load a SYLK file and return it as multi-array.
 *
 * @param   string  $sFile  File to load
 *
 * @return  mixed   Loaded multi-array or false on failure
 */
function load_sylk ( $sFile )
{
    if (!is_readable($sFile))
    {
        return false;
    }
    /*
        Open file.
    */
    if (($rFile = @fopen($sFile, 'r')) === false)
    {
        return false;
    }
    /*
        Loop on each line.
    */
    $reSYLK = "/^C;X([0-9]+);Y([0-9]+);K(\"(.*)\"|[0-9.,]+)$/u";
    $aLines = array();
    fgets($rFile); # skip header
    $iCol = 0;
    $iMax = 0;
    while (!feof($rFile))
    {
        $sLine = str_replace("\r", '', trim(fgets($rFile)));
        if (preg_match($reSYLK, $sLine, $aMatches))
        {
            /*
                Get column.
            */
            $aMatches[1] = intval($aMatches[1]);
            /*
                Get row.
            */
            $aMatches[2] = intval($aMatches[2]);
            /*
                Cleanup match.
            */
            if (isset($aMatches[4]))
            {
                $aMatches[3] = str_replace('""', '"', $aMatches[4]);
            }
            /*
                Create line if not exists.
            */
            if (!isset($aLines[$aMatches[2]]))
            {
                $aLines[$aMatches[2]] = array();
            }
            /*
                Save cell data.
            */
            $aLines[$aMatches[2]][$aMatches[1]] = $aMatches[3];
            $iCol = $aMatches[1];
            /*
                Save max column number.
            */
            $iMax = ($iCol > $iMax) ? $iCol : $iMax;
        }
    }
    fclose($rFile);
    /*
        Check and add missing cells.
    */
    foreach ($aLines as $row => &$line)
    {
        if (count($line) < $iMax)
        {
            for ($iCol = 1; $iCol <= $iMax; $iCol++)
            {
                if (!isset($line[$iCol]))
                {
                    $line[$iCol] = null;
                }
            }
        }
    }
    return $aLines;
}
// END load_sylk function

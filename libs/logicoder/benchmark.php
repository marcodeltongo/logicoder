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
 * Benchmark class.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/benchmark.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Benchmark
{
    /**
     * Initialization time
     */
    private $fInit;

    /**
     * Array with the timings
     */
    private $aTimings;

    /**
     * The number of decimals for the floats
     */
    private $nDecimals;

    /**
     * Constructor
     *
     * Initialize global timer.
     *
     * @param   float   $nInitTime  Initialization time
     * @param   integer $nDecimals  Number of decimals to use
     */
    public function __construct ( $nInitTime = true, $nDecimals = 5 )
    {
        $this->fInit = ($nInitTime === true) ? microtime(true) : $nInitTime;
        $this->aTimings = array();
        $this->nDecimals = $nDecimals;
    }

    /**
     * Starts counting.
     *
     * @param   string  $sName      Label for timing retrieval
     */
    public function start ( $sName )
    {
        if (isset($this->aTimings[$sName]))
        {
            $this->aTimings[$sName][] = array(microtime(true));
        }
        else
        {
            $this->aTimings[$sName] = array(array(microtime(true)));
        }
    }

    /**
     * Stops counting.
     *
     * @param   string  $sName      Label for timing retrieval
     */
    public function stop ( $sName )
    {
        $nDepth = count($this->aTimings[$sName]) - 1;
        $this->aTimings[$sName][$nDepth][] = microtime(true);
    }

    /**
     * Return execution time elapsed.
     *
     * @return  string  Formatted elapsed time
     */
    public function elapsed ( /* void */ )
    {
        return number_format(microtime(true) - $this->fInit, $this->nDecimals, '.', '');
    }

    /**
     * Returns all timings.
     *
     * @return  string  Formatted timings
     */
    public function timings ( /* void */ )
    {
        $fTotal = microtime(true) - $this->fInit;
        $sTrace = '';
        foreach ($this->aTimings as $sName => $aTimes)
        {
            $nDepth = count($this->aTimings[$sName]);
            $sTrace .= "\nTimer '$sName'" . (($nDepth == 1) ? ":\t" : " [$nDepth]:\t");
            $fTiming = 0;
            do
            {
                --$nDepth;
                $fTiming += ((isset($aTimes[$nDepth][1])) ? $aTimes[$nDepth][1] : microtime(true)) - $aTimes[$nDepth][0];
            } while ($nDepth > 0);
            $sTrace .= number_format($fTiming, $this->nDecimals, '.', '') . "s\t" . number_format($fTiming / ($fTotal / 100), 1) . '%';
        }
        $sTrace .= "\n\nTotal execution time: " . number_format($fTotal, $this->nDecimals, '.', '') . 's';
        return $sTrace;
    }
}
// END Logicoder_Benchmark class

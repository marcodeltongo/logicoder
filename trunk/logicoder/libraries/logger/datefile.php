<?php
/**
 * Logicoder Web Application Framework - DateFile Logger
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * File Logger class with date-based filenames.
 *
 * @package     Logicoder
 * @subpackage  Logger
 * @link        http://www.logicoder.com/documentation/logging.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Logger_DateFile extends Logicoder_Logger_File
{
    /**
     * Constructor.
     *
     * @param   number  $nThreshold Priority level limit
     * @param   string  $sPath      Path to destination dir
     */
    public function __construct ( $nThreshold = LOG_THRESHOLD, $sPath = LOG_PATH )
    {
        parent::__construct(realpath($sPath) . '/' . date('Y_m_d') . '.log', $nThreshold);
    }
}
// END Logicoder_DateFileLogger class

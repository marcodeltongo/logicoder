<?php
/**
 * Logicoder Web Application Framework - Models library components
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * File with Path Field
 *
 * A field whose choices are limited to files in a certain directory.
 * HTML input: text
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_FilePath extends Logicoder_Model_Field_Char
{
    /**
     * Required path to use for the file.
     */
    public $path       = '/tmp';

    /**
     * Optional wildcard to use to gather filenames.
     */
    public $match      = false;

    /**
     * Whether to descend in subdirs or not.
     */
    public $recursive  = false;
}
// END Logicoder_Model_Field_FilePath class

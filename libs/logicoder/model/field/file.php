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
 * File Field
 *
 * A file-upload field.
 * HTML input: file
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_File extends Logicoder_Model_Field_Char
{
    /**
     * The upload directory.
     */
    public $upload_to  = '/tmp';
}
// END Logicoder_Model_Field_File class

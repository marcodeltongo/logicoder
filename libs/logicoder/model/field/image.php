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
 * Image Field
 *
 * Like File field, but validates that the uploaded object is a valid image.
 * HTML input: file
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Image extends Logicoder_Model_Field_File
{
    /**
     * The image width.
     */
    public $width_field    = false;

    /**
     * The image height.
     */
    public $height_field   = false;
}
// END Logicoder_Model_Field_Image class

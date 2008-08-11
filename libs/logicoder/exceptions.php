<?php
/**
 * Logicoder Web Application Framework - Exceptions library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Simply an overloaded Exception.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Exception extends Exception { }
// END Logicoder_Exception class

/**
 * Exception for HTTP 404 Not found.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_404 extends Logicoder_Exception
{
    public function __construct ( $message = '404 - Not Found', $code = 404 )
    {
        parent::__construct($message, $code);
    }
}
// END Logicoder_404 class

/**
 * Exception for DB errors.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_DB_Exception extends Logicoder_Exception
{
}
// END Logicoder_DB_Exception class

/**
 * Exception for Model errors.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Exception extends Logicoder_Exception
{
}
// END Logicoder_Model_Exception class

/**
 * Exception for Model errors.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_RecordNotExists_Exception extends Logicoder_Model_Exception
{
    public function __construct ( $message = "The requested record don't exists.", $code = 0 )
    {
        parent::__construct($message, $code);
    }
}
// END Logicoder_Model_RecordNotExists_Exception class

/**
 * Exception for Model errors.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_RecordNonUnique_Exception extends Logicoder_Model_Exception
{
    public function __construct ( $message = "The passed fields values aren't unique.", $code = 0 )
    {
        parent::__construct($message, $code);
    }
}
// END Logicoder_Model_RecordNonUnique_Exception class

/**
 * Exception for Model errors.
 *
 * @package     Logicoder
 * @subpackage  Exceptions
 * @link        http://www.logicoder.com/documentation/exceptions.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Exception extends Logicoder_Model_Exception
{
}
// END Logicoder_Model_RecordNonUnique_Exception class

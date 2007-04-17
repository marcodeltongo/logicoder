<?php
/**
 * Logicoder Web Application Framework - Testing library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Is the script called from CLI ?
 * If so, output text compliant to TAP.
 */
if (!defined('PHP_CLI'))
{
    define('PHP_CLI', (PHP_SAPI == 'cli'));
}

// -----------------------------------------------------------------------------

/**
 * A testing and reporting class.
 *
 * The testing class is two-fold:
 * - Called from CLI it's compatible with the TAP protocol.
 * - Called via web it reports results as HTML and adds code coverage analysis.
 *
 * @package     Logicoder
 * @subpackage  Test
 * @link        http://www.logicoder.com/documentation/testing.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Test
{
    /**
     * The plan.
     */
    private $mPlan;

    /**
     * Flag for text mode vs html mode.
     */
    private $bText;

    /**
     * Total number of tests run.
     */
    private $nTestsRun;

    /**
     * Skip command countdown.
     */
    private $nSkipping;

    /**
     * Skipped tests stack.
     */
    private $aSkipped;

    /**
     * Todo command countdown.
     */
    private $nTodos;

    /**
     * Todo command message.
     */
    private $sTodoMsg;

    /**
     * Todo and bonus tests stack.
     */
    private $aTodos;

    /**
     * Bonus tests stack.
     */
    private $aBonus;

    /**
     * Total number of failures.
     */
    private $nFailures;

    /**
     * Failed tests stack.
     */
    private $aFailed;

    /**
     * Function callback to be run before a test.
     */
    private $runBefore = false;

    /**
     * Function callback to be run after a test.
     */
    private $runAfter = false;

    /**
     * Files for which we want code coverage reporting.
     */
    private $mFiles;

    /**
     * Array[File][Lines] of covered code.
     */
    private $aCovered;

    /**
     * The full path to the PHP interpreter.
     */
    private $sPHP = 'php5 ';

    /**
     * The starting time for benchmarking.
     */
    private $fStartTime;

    // -------------------------------------------------------------------------

    /**
     * Constructor.
     * Prepares the class and starts the testing/coverage.
     *
     * @param   string  $sName      Test suite name.
     * @param   mixed   $mFiles     List filenames to run code coverage analisys.
     */
    public function __construct ( $sName = false, $mFiles = false )
    {
        /*
            Start code coverage
        */
        $this->mFiles = false;
        $this->aCovered = false;
        if (function_exists('xdebug_start_code_coverage') and ($mFiles !== false))
        {
            xdebug_start_code_coverage(XDEBUG_CC_UNUSED); // | XDEBUG_CC_DEAD_CODE);
            switch (gettype($mFiles))
            {
                case 'array':
                    $this->mFiles = $mFiles;
                break;

                case 'string':
                    $this->mFiles = array($mFiles);
                break;

                case 'boolean':
                    $this->mFiles = $mFiles;
                break;
            }
        }
        /*
            Start timing
        */
        $this->fStartTime = microtime(true);
        /*
            Initialize empty plan data
        */
        $this->plan();
        /*
            Send headers if needed
        */
        $aHeaders = headers_list();
        if (in_array('Content-Type: text/plain', $aHeaders)
            or in_array('Content-Type: text/html', $aHeaders))
        {
            return;
        }
        /*
            Output as text
        */
        if (PHP_CLI)
        {
            $this->bText = true;
            header('Content-Type: text/plain');
            return;
        }
        /*
            Output as HTML
        */
        $this->bText = false;
        header('Content-Type: text/html');
        /*
            The name becomes the truncated path and filename
        */
        if ($sName === false)
        {
            $sDebug = debug_backtrace();
            $sName = basename($sDebug[0]['file'],'.php');
            unset($sDebug);
        }
        /*
            Output the first part of the html and the css definitions
        */
        $sHtml = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 TRANSITIONAL//EN">';
        $sHtml .= "\n<html>\n<head>\n<title>$sName ::: Logicoder_Test</title>\n<style type='text/css'>\n";
        $sHtml .= "body { background: #d3d7cf; font-family: Consolas,'Lucida Console', monospace; font-size: 11px; text-align: center; }\n";
        $sHtml .= "table { border-collapse: collapse; width: 760px;";
        $sHtml .= " font-size: 14px; margin: 0 auto; border: 1px solid #555753; text-align: left; }\n";
        $sHtml .= "td { padding: 2px 4px; }\n";
        $sHtml .= "h1 { color: #2e3436; }\n";
        $sHtml .= ".PLAN { background: #3465a4; color: white; font-size: 16px; font-weight: bold; font-variant: small-caps; }\n";
        $sHtml .= ".TNUM { text-align: right }\n";
        $sHtml .= ".PASS { background: #4e9a06; color: white; }\n";
        $sHtml .= ".TODO { background: #ce5c00; color: white; }\n";
        $sHtml .= ".FAIL { background: #a40000; color: white; }\n";
        $sHtml .= ".DIAG { background: #babdb6; color: black; }\n";
        $sHtml .= ".RESULTS { font-size: 16px; font-weight: bold; font-variant: small-caps; }\n";
        $sHtml .= ".RESULTS span { font-weight: normal; }\n";
        $sHtml .= ".CODE { font-weight: normal; font-size: 9px; background: #eeeeec; }\n";
        $sHtml .= ".COVERED { color: #4e9a06; }\n";
        $sHtml .= ".UNCOVERED { color: #a40000; }\n";
        $sHtml .= ".DEADCODE { background: #a40000; color: white; }\n";
        $sHtml .= ".COMMENT { color: #babdb6; }\n";
        $sHtml .= ".BRACKETS { color: #babdb6; }\n";
        $sHtml .= ".CODE th { padding: 2px; font-weight: normal; border-right: 1px solid #d3d7cf; }\n";
        $sHtml .= ".CODE td { padding: 2px 1em; }\n";
        $sHtml .= ".CODE pre { margin: 0; padding: 0; }\n";
        $sHtml .= "table a { color: white; text-decoration: none; }\n";
        $sHtml .= "hr { height: 32px; border: 0; }\n";
        $sHtml .= "</style>\n</head>\n<body>";
        $sHtml .= "\n<h1>$sName</h1>";
        $sHtml .= "\n<table cellspacing='1'>";
        echo $sHtml;
    }

    /**
     * Should we skip this test ?
     *
     * @return  boolean True if test should be skipped
     */
    protected function __skip ( /* void */ )
    {
        if ($this->nSkipping > 0)
        {
            /*
                One less to skip
            */
            --$this->nSkipping;
            return true;
        }
        /*
            Increment the number of tests run
        */
        ++$this->nTestsRun;
        /*
            Run the "magic" function if required to
        */
        $this->runBeforeTest();
        return false;
    }

    /**
     * Is this a todo test ?
     *
     * @return  mixed   The message string or false
     */
    protected function __todo ( /* void */ )
    {
        if ($this->nTodos > 0)
        {
            --$this->nTodos;
            return $this->sTodoMsg;
        }
        return false;
    }

    /**
     * Pretty prints a value.
     *
     * @param   mixed   $mWhat      Value to print
     * @return  string  A string with the formatted value
     */
    protected function __export ( $mWhat )
    {
        if (is_null($mWhat))
        {
            return 'NULL';
        }
        switch (gettype($mWhat))
        {
            case 'array':
                return str_replace("\n", '', var_export($mWhat, true));
            break;

            case 'object':
                return get_class($mWhat); // . str_replace('Object id', '', (string) $mWhat);
            break;

            default:
                return gettype($mWhat) . ' ' . var_export($mWhat, true);
        }
    }

    /**
     * Defines the testing plan.
     *
     * Can be called explicitly, thru end() or automatically at exit.
     *
     * @param   mixed   $mPlan      Number of expected tests, 'skip' or false
     * @param   string  $sReason    The reason to skip
     */
    public function plan ( $mPlan = false, $sReason = false )
    {
        /*
            Initialise object data
        */
        $this->mPlan = false;
        $this->sReport = '';
        $this->nTestsRun = 0;
        $this->nSkipping = 0;
        $this->aSkipped = array();
        $this->nTodos = 0;
        $this->sTodoMsg = '';
        $this->aTodos = array();
        $this->aBonus = array();
        $this->nFailures = 0;
        $this->aFailed = array();
        /*
            If an int is passed that's the number of tests expected
        */
        if (is_int($mPlan))
        {
            $this->mPlan = $mPlan;
            if ($this->bText)
            {
                echo '1..' . $mPlan . "\n";
            }
            else
            {
                echo '<tr class="PLAN"><td colspan="3">Running plan: 1..' . $mPlan . '</td></tr>' . "\n";
            }
        }
        elseif (is_string($mPlan))
        {
            $this->mPlan = 0;
            if ($this->bText)
            {
                echo '1..0' . ($sReason !== false) ? " # SKIP $sReason\n" : $mPlan . "\n";
            }
            else
            {
                echo '<tr class="PLAN"><td colspan="3">Skipping plan: ' . (($sReason !== false) ? $sReason : $mPlan) . '</td></tr>' . "\n";
            }
        }
        else
        {
            $this->mPlan = false;
        }
    }

    /**
     * Sets the function to be run before each test.
     *
     * @param   mixed   $mFunName   Function name, false to clear, null to run.
     */
    public function runBeforeTest ( $mFunName = null )
    {
        if (is_null($mFunName))
        {
            if ($this->runBefore !== false)
            {
                return call_user_func($this->runBefore);
            }
        }
        else
        {
            $this->runBefore = (function_exists($mFunName)) ? $mFunName : false;
        }
    }

    /**
     * Sets the function to be run after each test.
     *
     * @param   mixed   $mFunName   Function name, false to clear, null to run.
     */
    public function runAfterTest ( $mFunName = null )
    {
        if (is_null($mFunName))
        {
            if ($this->runAfter !== false)
            {
                return call_user_func($this->runAfter);
            }
        }
        else
        {
            $this->runAfter = (function_exists($mFunName)) ? $mFunName : false;
        }
    }

    /**
     * Pass a test.
     *
     * @param   string  $sMessage   The message to print
     * @param   string  $sInfo      Additional information to report
     */
    public function pass ( $sMessage, $sInfo = false )
    {
        /*
            Run the "magic" function if required to
        */
        $this->runAfterTest();
        /*
            Output
        */
        if ($this->bText)
        {
            $sTodo = $this->__todo();
            $sLine = 'ok ' . $this->nTestsRun . ((!empty($sMessage) and $sMessage[1] != '#') ? ' - ' . $sMessage : $sMessage );
            if ($sTodo !== false)
            {
                $sLine .= $sTodo;
                $this->aTodos[] = $this->nTestsRun;
                $this->aBonus[] = $this->nTestsRun;
            }
            echo $sLine . "\n";
            if ($sInfo !== false)
            {
                echo $sInfo . "\n";
            }
        }
        else
        {
            $sTodo = $this->__todo();
            if ($sTodo !== false)
            {
                $sInfo = ($sInfo !== false) ? $sInfo . $sTodo : $sTodo;
                $this->aTodos[] = $this->nTestsRun;
                $this->aBonus[] = $this->nTestsRun;
                $sLine = '<tr class="TODO">';
            }
            else
            {
                $sLine = '<tr class="PASS">';
            }
            $sLine .= '<td class="TNUM">' . $this->nTestsRun . '</td><td>' . $sMessage . '</td><td>';
            if ($sInfo !== false)
            {
                $sLine .=  ' ' . $sInfo;
            }
            else
            {
                $sLine .= '&nbsp;';
            }
            $sLine .=  '</td></tr>';
            echo $sLine . "\n";
        }
        return true;
    }

    /**
     * Fails a test.
     *
     * @param   string  $sMessage   The message to print
     * @param   string  $sInfo      Additional information to report
     */
    public function fail ( $sMessage, $sInfo = false )
    {
        /*
            Run the "magic" function if required to
        */
        $this->runAfterTest();
        /*
            Increment failures and save test number
        */
        ++$this->nFailures;
        $this->aFailed[] = $this->nTestsRun;
        /*
            Output
        */
        if ($this->bText)
        {
            $sTodo = $this->__todo();
            $sLine = 'not ok ' . $this->nTestsRun . ((!empty($sMessage) and $sMessage[1] != '#') ? ' - ' . $sMessage : $sMessage );
            if ($sTodo !== false)
            {
                $sLine .= $sTodo;
                $this->aTodos[] = $this->nTestsRun;
            }
            echo $sLine . "\n";
            if ($sMessage)
            {
                echo '#   Failed test "' . $sMessage . '"' . "\n";
            }
            else
            {
                echo '#   Failed test' . "\n";
            }
            $aTrace = debug_backtrace();
            foreach ($aTrace as $mStep)
            {
                if ($mStep['class'] == __CLASS__)
                {
                    $file = basename($mStep['file']);
                    $line = $mStep['line'];
                }
                else
                {
                    break;
                }
            }
            echo "#   in $file at line $line\n";
            if ($sInfo !== false)
            {
                echo $sInfo . "\n";
            }
        }
        else
        {
            $sTodo = $this->__todo();
            if ($sTodo !== false)
            {
                $sInfo = ($sInfo !== false) ? $sInfo . $sTodo : $sTodo;
                $this->aTodos[] = $this->nTestsRun;
                $sLine = '<tr class="TODO">';
            }
            else
            {
                $sLine = '<tr class="FAIL">';
            }
            $sLine .= '<td class="TNUM">' . $this->nTestsRun . '</td>';
            $sLine .= '<td>' . $sMessage . '</td>';
            $aTrace = debug_backtrace();
            foreach ($aTrace as $mStep)
            {
                if ($mStep['class'] == __CLASS__)
                {
                    $file = basename($mStep['file']);
                    $line = $mStep['line'];
                }
                else
                {
                    break;
                }
            }
            $sLine .= "<td>in $file at line $line";
            if ($sInfo !== false)
            {
                $sLine .=  ' ' . $sInfo;
            }
            $sLine .=  '</td></tr>';
            echo $sLine . "\n";
        }
        return false;
    }

    /**
     * Output diagnostic line(s)
     *
     * @param   mixed   $sMessage   A string or an array containing the message
     */
    public function diag ( $sMessage )
    {
        if ($this->bText)
        {
            if (is_array($sMessage))
            {
                echo '# ' . implode("\n# ", $sMessage) . "\n";
            }
            else
            {
                echo "# $sMessage" . "\n";
            }
        }
        else
        {
            if (is_array($sMessage))
            {
                $sMessage = implode("\n<br />", $sMessage);
            }
            $sMessage = "<tr class='DIAG'><td class='TNUM'>#</td><td>DIAG</td><td>$sMessage</td></tr>";
            echo $sMessage . "\n";
        }
        return true;
    }

    /**
     * Asserts the test is true.
     *
     * @param   bool
     * @param   string
     * @return  bool
     */
    public function ok ( $bTest, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }
        if (!is_bool($bTest))
        {
            $sTextInfo = "#          got: '" . $this->__export($bTest) . "'\n#     expected: 'boolean value'";
            $sHtmlInfo = "got " . $this->__export($bTest) . " while expected a boolean value";
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
        if ($bTest === true)
        {
            return $this->pass($sMessage);
        }
        else
        {
            return $this->fail($sMessage);
        }
    }

    /**
     * Asserts the test is false.
     *
     * @param   bool
     * @param   string
     * @return  bool
     */
    public function not ( $bTest, $sMessage = '' )
    {
        return $this->ok(!$bTest, $sMessage);
    }

    /**
     * Asserts the evaluated test is true.
     *
     * @param   string
     * @param   string
     * @return  bool
     */
    public function eval_ok ( $sTest, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }
        ob_start();
        $bRet = eval($sTest);
        ob_end_clean();
        if ($bRet === true)
        {
            return $this->pass($sMessage);
        }
        else
        {
            return $this->fail($sMessage);
        }
    }

    /**
     * Assert that A == B
     *
     * @param   mixed   $mTest
     * @param   mixed   $mExpect
     * @param   string  $sMessage
     */
    public function is ( $mTest, $mExpect, $sMessage)
    {
        if ($this->__skip())
        {
            return true;
        }
        if ($mTest == $mExpect)
        {
            return $this->pass($sMessage);
        }
        else
        {
            $sTextInfo = "#          got: '" . $this->__export($mTest) . "'\n#     expected: '" . $this->__export($mExpect) . "'";
            $sHtmlInfo = "got " . $this->__export($mTest) . " while expected " . $this->__export($mExpect);
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
    }

    /**
     * Assert that A != B
     *
     * @param   mixed   $mTest
     * @param   mixed   $mExpect
     * @param   string  $sMessage
     */
    public function isnt ( $mTest, $mExpect, $sMessage = '')
    {
        if ($this->__skip())
        {
            return true;
        }
        if ($mTest != $mExpect)
        {
            return $this->pass($sMessage);
        }
        else
        {
            $sTextInfo = "#         both: '" . $this->__export($mTest) . "'";
            $sHtmlInfo = "both are " . $this->__export($mTest);
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
    }

    /**
     * Assert that subject matches the regex pattern.
     *
     * @param   string  $sSubject
     * @param   regex   $rPattern
     * @param   string  $sMessage
     */
    public function like ( $sSubject, $rPattern, $sMessage = '')
    {
        if ($this->__skip())
        {
            return true;
        }
        if (preg_match($rPattern, $sSubject))
        {
            return $this->pass($sMessage);
        }
        else
        {
            $sTextInfo = "#                  '$sSubject'\n#    doesn't match '$rPattern'";
            $sHtmlInfo = "'$sSubject' doesn't match '$rPattern'";
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
    }

    /**
     * Assert that subject don't match the regex pattern.
     *
     * @param   string  $sSubject
     * @param   regex   $rPattern
     * @param   string  $sMessage
     */
    public function unlike ( $sSubject, $rPattern, $sMessage = '')
    {
        if ($this->__skip())
        {
            return true;
        }
        if (!preg_match($rPattern, $sSubject))
        {
            return $this->pass($sMessage);
        }
        else
        {
            $sTextInfo = "#                  '$sSubject'\n#          matches '$rPattern'";
            $sHtmlInfo = "'$sSubject' matches '$rPattern'";
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
    }

    /**
     * Confronts A to C using the B operator.
     *
     * @param   mixed   $mTest
     * @param   string  $sOp
     * @param   mixed   $mExpect
     * @param   string  $sMessage
     */
    public function cmp_ok ( $mTest, $sOp, $mExpect, $sMessage = '')
    {
        if ($this->__skip())
        {
            return true;
        }
        if (eval("return \$mTest $sOp \$mExpect;"))
        {
            return $this->pass($sMessage);
        }
        else
        {
            $sTextInfo = "#     " . $this->__export($mTest) . "\n#         $sOp" . "\n#     " . $this->__export($mExpect);
            $sHtmlInfo = $this->__export($mTest) . " isn't $sOp " . $this->__export($mExpect);
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
    }

    /**
     * Assert that object A has B method(s)
     *
     * @param   object  $oInstance
     * @param   mixed   $mMethods
     * @param   string  $sMessage
     */
    public function can_ok ( $oInstance, $mMethods, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }
        $bPass = true;
        $sErrors = '';

        if (is_array($mMethods))
        {
            foreach ($mMethods as $sMethod)
            {
                if (!method_exists($oInstance, $sMethod))
                {
                    $bPass = false;
                    $sErrors .= "\n#   method_exists(" . get_class($oInstance) . ', ' . $this->__export($sMethod) . ') failed';
                }
            }
        }
        else
        {
            if (!method_exists($oInstance, $mMethods))
            {
                $bPass = false;
                $sErrors .= "\n#   method_exists(" . get_class($oInstance) . ', ' . $this->__export($mMethods) . ') failed';
            }
        }
        if ($bPass)
        {
            return $this->pass($sMessage);
        }
        else
        {
            $sTextInfo = substr($sErrors, 1);
            $sHtmlInfo = str_replace("\n#   ", '<br />', $sErrors);
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
    }

    /**
     * Assert that object A is an instance of class B
     * or that value A is of type B
     *
     * @param   mixed   $mInstance
     * @param   string  $sClassType
     * @param   string  $sMessage
     */
    public function isa_ok ( $mInstance, $sClassType, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }
        if (is_object($mInstance))
        {
            if (($sClass = get_class($mInstance)) == $sClassType)
            {
                return $this->pass($sMessage);
            }
            else
            {
                $sTextInfo = "#   Object isn't a '$sClassType' instance, it's a '$sClass'.";
                $sHtmlInfo = "Object isn't a '$sClassType' instance, it's a '$sClass'.";
                return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
            }
        }
        else
        {
            if (($sType = gettype($mInstance)) == $sClassType)
            {
                return $this->pass($sMessage);
            }
            else
            {
                $sTextInfo = "#   Value isn't of type '$sClassType', it's a '$sType'.";
                $sHtmlInfo = "Value isn't of type '$sClassType', it's a '$sType'.";
                return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
            }
        }
    }

    /**
     * Assert that file name A is readable
     *
     * @param   string  $sFilename
     * @param   string  $sMessage
     */
    public function use_ok ( $sFileName, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }
        if (is_readable($sFileName))
        {
            return $this->pass($sMessage);
        }
        else
        {
            return $this->fail($sMessage, ($this->bText) ? "#   use $sFileName" : "use $sFileName");
        }
    }

    /**
     * Assert that file name A can be included
     *
     * @param   string  $sFilename
     * @param   string  $sMessage
     */
    public function include_ok ( $sFileName, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }

        ob_start();
        $bPass = include_once $sFileName;
        ob_end_clean();

        if ($bPass)
        {
            return $this->pass($sMessage);
        }
        else
        {
            return $this->fail($sMessage, ($this->bText) ? "#   include $sFileName" : "include $sFileName");
        }
    }

    /**
     * Assert that file name A can be required
     *
     * @param   string  $sFilename
     * @param   string  $sMessage
     */
    public function require_ok ( $sFileName, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }

        ob_start();
        $bPass = include_once $sFileName;    // NOTE: don't use require !
        ob_end_clean();

        if ($bPass)
        {
            return $this->pass($sMessage);
        }
        else
        {
            return $this->fail($sMessage, ($this->bText) ? "#   require $sFileName" : "require $sFileName");
        }

    }

    /**
     * Assert that A == B and reports differences
     *
     * @param   mixed   $sTest
     * @param   mixed   $sExpect
     * @param   string  $sMessage
     */
    public function is_deeply ( $mTest, $mExpect, $sMessage = '' )
    {
        if ($this->__skip())
        {
            return true;
        }
        if (gettype($mTest) != gettype($mExpect))
        {
            $sTextInfo = "#          got: '" . $this->__export($mTest) . "'\n#     expected: '" . $this->__export($mExpect) . "'";
            $sHtmlInfo = "<br />got '" . $this->__export($mTest) . "'<br />while expected '" . $this->__export($mExpect) . "'";
            return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
        }
        if ($mTest == $mExpect)
        {
            return $this->pass($sMessage);
        }
        switch (gettype($mTest))
        {
            case 'array':
                $sTextInfo = "#          got: '" . $this->__export($mTest) . "'\n#     expected: '" . $this->__export($mExpect) . "'";
                $sHtmlInfo = "<br />got '" . $this->__export($mTest) . "'<br />while expected '" . $this->__export($mExpect) . "'";
            break;

            case 'object':
                $class = get_class($mTest);
                if ($class != get_class($mExpect))
                {
                    $sTextInfo = "#          got: 'Instance of $class'\n#     expected: 'Instance of " . get_class($mExpect) . "'";
                    $sHtmlInfo = "<br />got 'Instance of $class'<br />while expected 'Instance of " . get_class($mExpect) . "'";
                    break;
                }
                $vars = get_class_vars($class);
                $sDiffs = '';
                foreach ($vars as $var => $default)
                {
                    if ($mTest->$var != $mExpect->$var)
                    {
                        $sDiffs .= "\n#          got: 'property $var = " . $this->__export($mTest->$var) . "'";
                        $sDiffs .= "\n#     expected: 'property $var = " . $this->__export($mExpect->$var) . "'";
                    }
                }
                $sTextInfo = substr($sDiffs, 1);
                $sHtmlInfo = str_replace("\n#   ", '<br />', $sDiffs);
            break;

            default:
                $sTextInfo = "#          got: '" . $this->__export($mTest) . "'\n#     expected: '" . $this->__export($mExpect) . "'";
                $sHtmlInfo = "<br />got '" . $this->__export($mTest) . "'<br />while expected '" . $this->__export($mExpect) . "'";
        }
        return $this->fail($sMessage, ($this->bText) ? $sTextInfo : $sHtmlInfo);
    }

    /**
     * Skip a number of tests
     *
     * @param   string  The message to print.
     * @param   integer How many ?
     */
    public function skip ( $sMessage = '', $nHowMany = 1 )
    {
        if ($nHowMany < 0)
        {
            $nHowMany = 0;
        }
        $this->nSkipping = $nHowMany;
        while ($nHowMany-- > 0)
        {
            $this->aSkipped[] = ++$this->nTestsRun;
            if ($this->bText)
            {
                $this->pass(' # SKIP ' . $sMessage);
            }
            else
            {
                $sTodo = $this->__todo();
                if ($sTodo !== false)
                {
                    $this->aTodos[] = $this->nTestsRun;
                    $sLine = '<tr class="TODO">';
                }
                else
                {
                    $sLine = '<tr class="DIAG">';
                }
                $sLine .= "<td class='TNUM'>{$this->nTestsRun}</td><td>SKIP</td><td>$sMessage$sTodo</td></tr>";
                echo $sLine . "\n";
            }
        }
        return true;
    }

    /**
     * Adds todo command to a number of tests that should fail
     *
     * @param   string
     * @param   integer
     */
    public function todo ( $sMessage = '', $nHowMany = 1 )
    {
        if ($nHowMany < 0)
        {
            $nHowMany = 0;
        }
        $this->nTodos = $nHowMany;
        $this->sTodoMsg = ' # TODO ' . $sMessage;
        return true;
    }

    /**
     * Adds todo command to a number of tests to skip
     *
     * @param   string
     * @param   integer
     */
    public function todo_skip ( $sMessage = '', $nHowMany = 1 )
    {
        $this->todo($sMessage, $nHowMany);
        $this->skip('', $nHowMany);
        return true;
    }

    /**
     * Print code coverage report
     */
    public function coverage_report ( /* void */ )
    {
        /*
            Collect coverage data and stop XDebug
        */
        $this->aCovered = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        /*
            Loop over files covered
        */
        foreach ($this->aCovered as $sFile => $aLines)
        {
            /*
                Clean file name.
            */
            $sFilename = basename($sFile);
            /*
                Skip self test if not requested
            */
            if (!defined('LOGICODER_SELF_TEST'))
            {
                if (($this->mFiles !== true and !in_array($sFilename, $this->mFiles)) or $sFile == __FILE__)
                {
                    continue;
                }
            }
            elseif ($sFile != __FILE__)
            {
                continue;
            }
            /*
                Start file report
            */
            echo "\n<hr />\n<table class='CODE' id='$sFilename'><tr class='RESULTS PLAN'><td colspan='2'>Code coverage report for $sFilename</td></tr>" . "\n";
            $aSrc = file($sFile);
            $nSrc = count($aSrc);
            $nCLines = 0;
            $nULines = 0;
            $nComments = 0;
            /*
                Loop on source file lines
            */
            foreach ($aSrc as $nLine => $sLine)
            {
                /*
                    Get line type
                */
                #if (($sClass = $this->__coverage_line(++$nLine, trim($sLine), $aLines)) == '')
                if (($sClass = $this->__line_coverage(++$nLine, trim($sLine), $aLines)) == '')
                {
                    ++$nComments;
                    continue;
                }
                /*
                    Increment counters as needed
                */
                switch ($sClass)
                {
                    case '':
                    case 'COMMENT':
                    case 'BRACKETS':
                        ++$nComments;
                    break;

                    case 'COVERED':
                        ++$nCLines;
                    break;

                    case 'UNCOVERED':
                        ++$nULines;
                    break;
                }
                /*
                    Print it
                */
                $sLine = '<pre>' . htmlentities(str_replace(array("\n"), '', $sLine)) . '</pre>';
                echo "<tr class='$sClass'><th style='text-align: right;'>$nLine</th><td>$sLine</td></tr>" . "\n";
            }
            /*
                Coverage percentage and "VOTE"
            */
            $nCPC = number_format(($nCLines / ($nSrc - $nComments)) * 100, 2);
            echo "<tr class='PLAN'><td colspan='2'>$nCLines covered lines, $nComments commented lines, $nULines not covered !</td></tr>\n";
            if ($nCPC < 30)
            {
                echo "<tr class='PLAN FAIL'><td colspan='2'>VERY POOR CODE COVERAGE &raquo; $nCPC&#37;</td></tr>\n</table>" . "\n";
            }
            if ($nCPC < 50)
            {
                echo "<tr class='PLAN FAIL'><td colspan='2'>POOR CODE COVERAGE &raquo; $nCPC&#37;</td></tr>\n</table>" . "\n";
            }
            elseif ($nCPC < 75)
            {
                echo "<tr class='PLAN TODO'><td colspan='2'>GOOD CODE COVERAGE &raquo; $nCPC&#37;</td></tr>\n</table>" . "\n";
            }
            elseif ($nCPC < 95)
            {
                echo "<tr class='PLAN TODO'><td colspan='2'>VERY GOOD CODE COVERAGE &raquo; $nCPC&#37;</td></tr>\n</table>" . "\n";
            }
            else
            {
                echo "<tr class='PLAN PASS'><td colspan='2'>SUPERB CODE COVERAGE &raquo; $nCPC&#37;</td></tr>\n</table>" . "\n";
            }
        }
    }

    /**
     * Returns line type and code coverage information
     *
     * @param   int     $nLine      Line index number
     * @param   string  $sLine      The line text
     * @param   array   $aData      Coverage data
     */
    protected function __line_coverage  ( $nLine, $sLine, &$aData )
    {
        static $bComment  = false;
        /*
            Skip empty lines and alone brackets
        */
        switch ($sLine)
        {
            case '':
                return '';
            break;

            case '{':
            case '}':
            case '<?':
            case '<?php':
            case '?>':
                return 'BRACKETS';
            break;
        }
        /*
            Is line covered ?
        */
        if (isset($aData[$nLine]))
        {
            /*
                Gather information from XDebug
            */
            return ($aData[$nLine] == -1) ? 'UNCOVERED' : 'COVERED';
        }
        else
        {
            /*
                Match standalone comments
            */
            if (strpos($sLine, '*/') === 0)
            {
                $bComment = false;
                return 'COMMENT';
            }
            if ($bComment or (strpos($sLine, '//') === 0) or (strpos($sLine, '#') === 0))
            {
                return 'COMMENT';
            }
            if (strpos($sLine, '/*') === 0)
            {
                $bComment = true;
                return 'COMMENT';
            }
            /*
                Fire up the tokenizer !
            */
            $mTokens = token_get_all('<?php ' . $sLine . ' ?>');
            foreach ($mTokens as $mToken)
            {
                if (is_string($mToken))
                {
                    continue;
                }
                switch ($mToken[0])
                {
                    case T_PRIVATE:                 // private
                    case T_PUBLIC:                  // public
                    case T_PROTECTED:               // protected
                    case T_FUNCTION:                // function
                    case T_CLASS:                   // class
                    case T_REQUIRE:                 // require
                    case T_REQUIRE_ONCE:            // require_once
                    case T_INCLUDE:                 // include
                    case T_INCLUDE_ONCE:            // include_once
                        return 'COVERED';
                    break;
                }
            }
        }
        return $sClass;
    }

    /**
     * Set the PHP interpreter to use
     *
     * @param   string  $sPathname  The full path to the PHP interpreter
     */
    public function php ( $sPathname = false )
    {
        if ($sPathname !== false)
        {
            $this->sPHP = $sPathname . ' ';
        }
        return $this->sPHP;
    }

    /**
     * Run a single test
     *
     * @param   string  $sPathname  The full path to the test file
     * @param   string  $sRelPath   Relative path to prefix to links
     */
    public function run ( $sPathname, $sRelPath = '' )
    {
        if ($this->__skip())
        {
            return 0;
        }
        /*
            Check file
        */
        if (!file_exists($sPathname) or !is_readable($sPathname))
        {
            return $this->fail($sPathname, ((PHP_CLI) ? '#   ' : ' &raquo; ')
                                            . "File doesn't exists or can't be read !");
        }
        /*
            Switch to test directory
        */
        $oldir = getcwd();
        chdir(realpath(dirname($sPathname)));
        /*
            Launch the test
        */
        $nExitCode = 0;
        $aOutput = array();
        exec($this->sPHP . $sPathname, $aOutput, $nExitCode);

        if (!PHP_CLI)
        {
            $sPathname = "<a href='$sRelPath$sPathname'>&raquo; $sRelPath$sPathname</a>";
        }
        /*
            Export the results
        */
        switch ($nExitCode)
        {
            case 0:
                /*
                    Success
                */
                $this->pass($sPathname);
            break;

            case 255:
                /*
                    Bailed out or parse error
                */
                $this->fail($sPathname, ((PHP_CLI) ? '#   ' : '<br />&raquo; ') . "BAILED OUT or PARSE ERROR !");
            break;

            default:
                /*
                    Some tests failed
                */
                $this->fail($sPathname, ((PHP_CLI) ? '#   ' : '<br />&raquo; ') . "$nExitCode tests failed");
        }
        chdir($oldir);
        return $nExitCode;
    }

    /**
     * Run all tests in a directory
     *
     * @param   string  $sDirname   Directory to scan for tests
     * @param   regex   $rFilter    RegEx filter to apply to filenames
     * @param   array   $aExcept    Array of files to skip
     * @param   boolean $bRecursive Whether is a recursive call or not
     */
    public function all_in ( $sDirname = '.', $rFilter = '|_t.php|', $aExcept = array(), $bRecursive = false )
    {
        /*
            Get the directory
        */
        $dir = opendir($sDirname);
        /*
            Switch to tests directory
        */
        $oldir = getcwd();
        chdir(realpath($sDirname));
        /*
            Iterate
        */
        while ($sFile = readdir($dir))
        {
            if (is_dir($sFile))
            {
                /*
                    Descend if it's a sub-directory
                */
                if (!stristr($sFile, "."))
                {
                    $this->all_in($sFile, $rFilter, $aExcept, true);
                }
                /*
                    Skip if not a file nor a directory or isn't readable
                */
                continue;
            }
            /*
                Apply filters
            */
            if (in_array($sFile, $aExcept) or !preg_match($rFilter, $sFile))
            {
                continue;
            }
            /*
                Run the test file
            */
            $this->run($sFile, ($bRecursive) ? $sDirname.'/' : '');
        }
        closedir($dir);
        chdir($oldir);
    }

    /**
     * Exit due to some serious problem
     *
     * @param   string  $sReason
     */
    public function BAIL_OUT ( $sReason = 'REASON TO BAIL OUT !' )
    {
        if ($this->bText)
        {
            echo 'Bail out! ' . $sReason . "\n";
        }
        else
        {
            echo "<tr class='FAIL DIAG'><td colspan='3'>BAIL OUT !<br /><em>$sReason</em></td></tr></table>" . "\n";
        }
        $this->nTestsRun = 0;    // Needed to block call to end()
        exit(255);
    }

    /**
     * Close the test suite and generate output
     */
    public function end ( /* void */ )
    {
        $mRet = 0;
        if ($this->nTestsRun == 0)
        {
            return 0;
        }
        if (($this->mPlan === false) and $this->bText)
        {
            echo '1..' . $this->nTestsRun . "\n";
        }
        if ($this->bText)
        {
            if ($this->nFailures > 0)
            {
                echo '# Looks like you failed ' . $this->nFailures . ' tests of ' . $this->nTestsRun . "\n";
            }
            else
            {
                echo '# ALL ' . $this->nTestsRun . ' PASSED !' . "\n";
            }

        }
        else
        {
            $sTestsOk = number_format((100 / $this->nTestsRun) * ($this->nTestsRun - $this->nFailures), 2);
            echo "\n<tr><td colspan='3'>&nbsp;</td></tr>";
            echo "\n<tr class='RESULTS PLAN'><td colspan='2'>Total</td><td>{$this->nTestsRun}";
            echo " tests run, $sTestsOk &#37; gone okay.</td></tr>\n";
            if ($this->nFailures > 0)
            {
                echo "<tr class='RESULTS FAIL'><td colspan='2'>Failed</td><td>{$this->nFailures} <span>[";
                echo implode(', ', $this->aFailed) . "]</span></td></tr>\n";
            }
            if (count($this->aSkipped) > 0)
            {
                echo "<tr class='RESULTS DIAG'><td colspan='2'>Skipped</td><td>" . count($this->aSkipped);
                echo " <span>[" . implode(', ', $this->aSkipped) . "]</span></td></tr>\n";
            }
            if (count($this->aTodos) > 0)
            {
                echo "<tr class='RESULTS TODO'><td colspan='2'>Todo</td><td>" . count($this->aTodos);
                echo " <span>[" . implode(', ', $this->aTodos) . "]</span></td></tr>\n";
            }
            if (count($this->aBonus) > 0)
            {
                echo "<tr class='RESULTS PASS'><td colspan='2'>Bonus</td><td>" . count($this->aBonus);
                echo " <span>[" . implode(', ', $this->aBonus) . "]</span></td></tr>\n";
            }
        }
        if (!$this->bText)
        {
            echo '</table>' . "\n";
            if (function_exists('xdebug_get_code_coverage') and $this->mFiles !== false)
            {
                $this->coverage_report();
            }
            $sTime = number_format(microtime(true) - $this->fStartTime, 3, '.', '');
            echo '<pre>REPORT GENERATED IN ' . $sTime . ' SECONDS BY LOGICODER_TEST ON ' .
                    strtoupper(date(DATE_RFC822)) . '</pre>' . "\n";
            echo '</body></html>' . "\n";
        }
        $mRet = ($this->nFailures > 254) ? 254 : $this->nFailures;
        $this->nTestsRun = 0;    // Just to block call to end() on __destruct()
        return $mRet;
    }

    /**
     * If needed call end
     *
     * @ignore
     */
    public function __destruct ( /* void */ )
    {
        exit($this->end());
    }
}
// END Logicoder_Test class

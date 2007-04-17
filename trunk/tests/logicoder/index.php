<?php
/**
 * Run all tests in this directory and sub-directories.
 */

/*
    Force console-like output.
    Default: not defined -> autodetect
*/
#define('PHP_CLI', true);

/*
    Include the test library.
*/
require(dirname(__FILE__) . '/../../logicoder/libraries/test.php');

/*
    Get a new instance.
*/
$t = new Logicoder_Test('Running all Logicoder tests.');

/*
    Tell to use a particular PHP interpreter.
    Default: php5
*/
#$t->php('/opt/lampp/bin/php');

/*
    Run all test files in this directory.
    Params: $sDirname = '.', $rFilter = '|_t.php|', $aExcept = array()
*/
$t->all_in();

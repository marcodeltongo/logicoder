<?php
/**
 * Just a simple test example.
 */

/*
    Include the test library.
*/
require('../../logicoder/libraries/test.php');

/*
    Get a new instance.
*/
$t = new Logicoder_Test('Test example.', true);

/*
    Pretty fake test.
*/
$t->ok(true, 'ok');

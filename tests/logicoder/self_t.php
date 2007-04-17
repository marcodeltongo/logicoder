<?php
/**
 * Logicoder_Test self-test.
 */

/*
    Start XDebug Code Coverage earlier since we need to test ourself.
*/
if (function_exists('xdebug_start_code_coverage'))
{
    xdebug_start_code_coverage(XDEBUG_CC_UNUSED);
}

/*
    Activate self coverage
*/
define('LOGICODER_SELF_TEST', true);

/*
    Include the test library.
*/
require(dirname(__FILE__) . '/../../logicoder/libraries/test.php');

/*
    Get a new instance.
*/
$t = new Logicoder_Test('Self-testing Logicoder_Test.', true);

/*
    This function will be run before each test.
*/
function runBeforeTest ()
{
    return;
}
/*
    This function will be run after each test.
*/
function runAfterTest ()
{
    return;
}

/*
    Define PLAN.
*/
$t->plan(3);
$t->runBeforeTest("runBeforeTest");
$t->runAfterTest("runAfterTest");

/*
    OK
*/
$t->diag('OK');
$t->ok(true, 'true');
$t->ok($t == $t, '$t == $t');
$t->ok($t === $t, '$t === $t');

/*
    !OK
*/
$t->diag('!OK');
$t->ok(!(false), 'false');
$t->ok(!($t != $t), '$t != $t');
$t->ok(!($t !== $t), '$t !== $t');
$t->ok(!($t === $o), '$t === $o');

/*
    NOT
*/
$t->diag('NOT');
$t->not(false, 'false');
$t->not($t != $t, '$t != $t');
$t->not($t !== $t, '$t !== $t');
$t->not($t === $o, '$t === $o');

/*
    EVAL_OK
*/
$t->diag(array('EVAL_OK'));
$t->eval_ok('return true;', 'return true');

/*
    IS
*/
$t->diag('IS');
$t->is(true, true, 'true, true');
$t->is($t, $t, '$t, $t');

/*
    ISNT
*/
$t->diag('ISNT');
$t->isnt(true, false, 'true, false');
$t->isnt($t, $o, '$t, $o');

/*
    LIKE
*/
$t->diag('LIKE');
$t->like('', '||', 'LIKE ?');

/*
    UNLIKE
*/
$t->diag('UNLIKE');
$t->unlike('', '|\w|', 'UNLIKE ?');

/*
    CMP_OK
*/
$t->diag('CMP_OK');
$t->cmp_ok($t, '===', $t, 'CMP_OK');

/*
    CAN_OK
*/
$t->diag('CAN_OK');
$t->can_ok($t, 'can_ok', 'CAN_OK');

/*
    ISA_OK
*/
$t->diag('ISA_OK');
$t->isa_ok($t, 'Logicoder_Test', "It's a Test instance !");

/*
    USE_OK
*/
$t->diag('USE_OK');
$t->use_ok(__FILE__, "Can use file.");

/*
    INCLUDE_OK
*/
$t->diag('INCLUDE_OK');
$t->include_ok(__FILE__, "Can include file.");

/*
    REQUIRE_OK
*/
$t->diag('REQUIRE_OK');
$t->require_ok(__FILE__, "Can require file.");

/*
    IS_DEEPLY
*/
$t->diag('IS_DEEPLY');
$t->is_deeply($t, $t, 'Twins ?');

/*
    SKIP
*/
$t->diag('SKIP');
$t->skip('Skippie !', 1);
$t->ok(true);

/*
    TODO
*/
$t->diag('TODO');
$t->todo('TODO !', 1);
$t->ok(true);

/*
    TODO_SKIP
*/
$t->diag('TODO_SKIP');
$t->todo_skip('TODO_SKIP !', 1);
$t->ok(true);

/*
    PHP
*/
$t->diag('PHP');
$t->php('php5 ');

/*
    RUN
*/
$t->diag('RUN');
$t->run('example_t.php');

<?php
/**
 * RUN AUTOMATED TESTS
 **/

/**
 * By Dawood Morris Kaundama
 * Jun 2021 09:31:01 SAST
 **/
$startTime = microtime(true);

print "Running tests...\n";

require_once 'tests/tests.php';

$Tests = new Tests(tests: $tests, _argv: $argv);

$Tests->run();

$timeElapsed = round(microtime(true)-$startTime,2);

if($Tests->wereRun()) {
    print "Done running tests in {$timeElapsed} seconds.\n";
}

$Tests->results();

?>
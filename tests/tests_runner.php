<?php
/**
 * The test runner
 * @author Dawood Morris Kaundama
 **/
class Tests {

	private const API_NAME = 'Neuron API';
	private array $passed = [];
	private array $failed = [];

	private bool $testsWereRun = false;

	private int $allTests = 0;

	//The tests array
	private array $tests = [];

	//cli options and flags
	private array $_argv = [];

	/**
	 * CLI argument handlers
	 **/
	private const CLI_ARGS_HANDLERS = [
		'--skip-endpoints' => 'testButSkipEndpointTests',
		'--skip-actions' => 'testButSkipSomeActionTests',
		'--skip-both' => 'testButSkipSomeEndpointsAndSomeActionTests',
		'--all' => 'runAllTests'
	];

	public function __construct(array $tests, array $_argv) {
		$this->tests = $tests;
		$this->_argv = $_argv;
	}

	/**
	 * Run the tests
	 **/
	public function run(): void {
		$cliArgs = $this->parseCLIArgs();
		if($cliArgs->hasArgs) {
			foreach($cliArgs->args as $arg) {
				$handler = Tests::CLI_ARGS_HANDLERS[$arg]??'unKnownCLIOption';
				$this->$handler(arg: $arg);
			}
		} else {
			$this->testsWereRun = true;
			foreach($this->tests as $test) {
				print "Testing Endpoint '{$test['endpoint']}':\n";
				require_once "_tests/{$test['endpoint']}.php";
				foreach($test['actionTests'] as $actionTest) {
					$this->allTests++;
					print "Test #{$this->allTests}: [action: {$actionTest}]\n";
					$results = $actionTest();
					if($results) {
						print "Test OK ✅\n";
						$this->passed[] = $actionTest;
					} else {
						print "Test FAILED ❌\n";
						$this->failed[] = $actionTest;
					}
					print "\n";
				}
			}
		}
	}

	/*****
	 * /////////////////////////////////
	 * CLI Arg HANDLERS DEFINITION START HERE
	 * /////////////////////////////////
	 * */

	/**
	 * Run all tests
	 **/
	private function runAllTests(string $arg): void {
		foreach($this->tests as $test) {
			$this->testsWereRun = true;
			print "Testing Endpoint '{$test['endpoint']}':\n";
			require_once "_tests/{$test['endpoint']}.php";
			foreach($test['actionTests'] as $actionTest) {
				$this->allTests++;
				print "Test #{$this->allTests}: [action: {$actionTest}]\n";
				$results = $actionTest();
				if($results) {
					print "Test OK ✅\n";
					$this->passed[] = $actionTest;
				} else {
					print "Test FAILED ❌\n";
					$this->failed[] = $actionTest;
				}
				print "\n";
			}
		}
	}

	/**
	 * Run tests, but exclude the given tests in the skip_tests.json config file
	 **/
	private function testButSkipEndpointTests(string $arg): void {
		if(file_exists('tests/skip_tests.json')) {
			$skipEndpoints = json_decode(file_get_contents('tests/skip_tests.json'));
			print "The following endpoints will be skipped:\n";
			print json_encode($skipEndpoints)."\n";
			foreach($this->tests as $test) {
				if(!in_array($test['endpoint'], $skipEndpoints)) {
					$this->testsWereRun = true;
					print "Testing Endpoint '{$test['endpoint']}':\n";
					require_once "_tests/{$test['endpoint']}.php";
					foreach($test['actionTests'] as $actionTest) {
						$this->allTests++;
						print "Test #{$this->allTests}: [action: {$actionTest}]\n";
						$results = $actionTest();
						if($results) {
							print "Test OK ✅\n";
							$this->passed[] = $actionTest;
						} else {
							print "Test FAILED ❌\n";
							$this->failed[] = $actionTest;
						}
						print "\n";
					}
				}
			}
		} else {
			print "No skip_tests.json config file was found. Please create one then run tests with the --skip option.\n";
		}
	}

	/**
	 * Run tests, but exclude the given tests in the skip_tests.json config file
	 **/
	private function testButSkipSomeActionTests(string $arg): void {
		if(file_exists('tests/skip_action_tests.json')) {
			$skipActionTests = json_decode(file_get_contents('tests/skip_action_tests.json'));
			print "The following action tests will be skipped:\n";
			print json_encode($skipActionTests)."\n";
			foreach($this->tests as $test) {
				$this->testsWereRun = true;
				print "Testing Endpoint '{$test['endpoint']}':\n";
				require_once "_tests/{$test['endpoint']}.php";
				foreach($test['actionTests'] as $actionTest) {
					if(!in_array($actionTest, $skipActionTests)) {
						$this->allTests++;
						print "Test #{$this->allTests}: [action: {$actionTest}]\n";
						$results = $actionTest();
						if($results) {
							print "Test OK ✅\n";
							$this->passed[] = $actionTest;
						} else {
							print "Test FAILED ❌\n";
							$this->failed[] = $actionTest;
						}
						print "\n";
					}
				}
			}
		} else {
			print "No skip_action_tests.json config file was found. Please create one then run tests with the --skip option.\n";
		}
	}

	/**
	 * Run tests, but exclude the given tests in the skip_tests.json config file
	 **/
	private function testButSkipSomeEndpointsAndSomeActionTests(string $arg): void {
		if(file_exists('tests/skip_both.json')) {
			$skipsConfigs = json_decode(file_get_contents('tests/skip_both.json'));
			print json_last_error();
			print "The following endpoints will be skipped:\n";
			print json_encode($skipsConfigs->endpoints)."\n";
			print "The following actions will be skipped:\n";
			print json_encode($skipsConfigs->actions)."\n";
			foreach($this->tests as $test) {
				if(!in_array($test['endpoint'], $skipsConfigs->endpoints)) {
					$this->testsWereRun = true;
					print "Testing Endpoint '{$test['endpoint']}':\n";
					require_once "_tests/{$test['endpoint']}.php";
					foreach($test['actionTests'] as $actionTest) {
						if(!in_array($actionTest, $skipsConfigs->actions)) {
							$this->allTests++;
							print "Test #{$this->allTests}: [action: {$actionTest}]\n";
							$results = $actionTest();
							if($results) {
								print "Test OK ✅\n";
								$this->passed[] = $actionTest;
							} else {
								print "Test FAILED ❌\n";
								$this->failed[] = $actionTest;
							}
							print "\n";
						}
					}
				}
			}
		} else {
			print "No skip_both.json config file was found. Please create one then run tests with the --skip-both option.\n";
		}
	}

	/**
	 * Handle when some unknown handler is specified
	 * @param $arg (string) The current argument/cli option being processed
	 **/
	private function unKnownCLIOption(string $arg): void {
		print "Unknown CLI option '{$arg}' specified.\n";
		$this->usage();
	}

	/**
	 * Tell the user when is this all about
	 **/
	private function usage(): void {
		print "Usage:\n";
		print "This is a test runner used to run automated tests on the ".Tests::API_NAME.".\n";
		print "To run it, type on the cli: ./test [--skip-endpoints | --skip-actions | --skip-both]\n";
		print "Where [options] can be:\n";
		print "--skip-endpoints: To skip some endpoints. The skip_tests.json config file must be defined.\n";
		print "--skip-actions: to skip testing some actions on endpoints. The skip_action_tests.json config file must be defined.\n";
		print "--skip-both: to skip testing some endpoints and actions on endpoints. The skip_both.json config file must be defined.\n";
		print "All config files must be defined in the tests directory.\n";
	}

	/**
	 * Chect to see if CLI arguments were passed.
	 **/
	private function parseCLIArgs(): object {
		$cliArgs = new stdClass;
		$cliArgs->hasArgs = false;
		$argvCount = count($this->_argv);
		if($argvCount > 1) {
			$cliArgs->hasArgs = true;
			$args = explode(' ', trim($this->_argv[1]));
			$cliArgs->args = $args;
		}
		return $cliArgs;
	}

	/**
	 * Whether tests were run or not
	 **/
	public function wereRun(): bool {
		return $this->testsWereRun;
	}


	/**
	 * Collect and Report Results
	 **/
	public function results(): void {
		if(!$this->testsWereRun) return;
		print "\n";
		print "Test Results:\n";
		if($this->allTests === 0) {
			print "\n";
			print "❌❌❌❌❌❌❌❌❌ NO TESTS WERE RUN AS THEY WERE NOT SUPPLIED ❌❌❌❌❌❌❌❌❌\n";
			return;
		}
		if($this->allTests === count($this->passed)) {
			print "\n";
			print "✅✅✅✅✅✅✅✅✅ ALL TESTS PASSED AND ALL IS OK 👌 ✅✅✅✅✅✅✅✅✅\n";
			$passedCount = count($this->passed);
			$failedCount = count($this->failed);
			print "{$passedCount}/{$this->allTests} TESTS PASSED\n";
			print "{$failedCount}/{$this->allTests} TESTS FAILED\n";
			print "🥰🥰🥰🥰🥰🥰🥰🥰🥰 GOOD JOB 🥰🥰🥰🥰🥰🥰🥰🥰🥰\n";
		} else {
			$passedCount = count($this->passed);
			$failedCount = count($this->failed);
			print "\n";
			print "❌❌❌❌❌❌❌❌❌ SOME TESTS FAILED ❌❌❌❌❌❌❌❌❌\n";
			print "{$passedCount}/{$this->allTests} TESTS PASSED\n";
			print "{$failedCount}/{$this->allTests} TESTS FAILED\n";
		}

	}
}

?>
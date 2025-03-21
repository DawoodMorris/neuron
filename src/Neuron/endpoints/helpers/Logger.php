<?php
/**
 * Endpoint: Logger
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions is defined in each action method
 * Documentation: 
 **/
class Logger {
	//data for this endpoint to work with
	private object $data;

	//results of the the requested action on this endpoint
	private array $results = [];
	private string $logiFile;
	private const ACTIONS = [
	];

	function __construct(object $data) {
		$this->data = $data;
		$this->results['status'] = false;
		$this->logiFile = NEURON_ROOT.'logs/platform_logs.log';
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: []);
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
		$action = Logger::ACTIONS[$this->data->action] ?? 'inValidAction';
		return $this->$action();
	}

	/**
	 * Respond to an invalid given action
	 **/
	private function inValidAction(): array {
		return [
			'status' => false,
			'error' => 'inValidAction',
			'errorMessage' => MESSAGES['inValidAction']
		];
	}

	/**
	 * Close database connections if any were defined. Database connections must be explicitly given when being called
	 * if database connections were defined
	 * @param $databases (array) Database connections to close
	 **/
	private function closeDatabaseConnections(array $databases): void {
		if(is_array($databases) && count($databases) > 0) {
			foreach($databases as $database) {
				if(method_exists($database,'close')) {
					$database->close();
				}
			}
		}
	}


	/**
	 * Action implementations start here...
	 **/

	/**
	 * Log a message to the Nervous/logs/platform_logs.log file
	 * @param $tag (string) The tag for the log. Useful to be a function name, or class name
	 * @param $msg (string) The log message
	 **/
	public function log(string $tag, string $msg): void {
		$currentTime = date('Y-m-d h-i-sa');
		$_msg = '['.$currentTime.']['.$tag.']: '.$msg."\n";
		file_put_contents($this->logiFile, $_msg, FILE_APPEND);
	}
}

$Logger = new Logger(data: new stdClass);
?>
<?php
/**
 * Endpoint: Test
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions
 **/
class Test {

	//payload for this endpoint to work with
	private object $payload;

	//results of the the requested action on this endpoint
	private array $results = [];

	private object $db;
	private object $DBBridge;

	function __construct(object $payload) {
		$this->payload = $payload;
		$this->results['status'] = false;
		$this->db = @dBConnection(database: 'test_db');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		global $Logger;
		$Logger->log(tag: 'Test->__construct', msg: json_encode($this->payload));
		$this->DBBridge = new DBBridge(db: $this->db);
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: [$this->db]);
	}

	/**
	 * Process the action
	 **/
	public function process() {
		$action = method_exists(get_class($this), ($this->payload->action??'noAction')) ? $this->payload->action : 'inValidAction';
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
	 * The ping action
	 **/
	private function ping(): array {
		return [
			'status' => true,
			'message' => MESSAGES['pingSuccess']
		];
	}

	/**
	 * The ping action
	 **/
	private function test(): array {
		$InputValidator = new InputValidator(data: $this->payload->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.'.$this->payload->action);
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
		return [
			'status' => true,
			'message' => MESSAGES['pingSuccess'],
			'yourInfo' => $this->payload->data->info
		];
	}
}
?>
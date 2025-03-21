<?php
/**
 * Endpoint: SystemLogs
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions is defined in each action method
 * Documentation: 
 **/
class SystemLogs {
	//data for this endpoint to work with
	private object $data;

	//results of the the requested action on this endpoint
	private array $results = [];

	private object $db;
	private object $DBBridge;
	private string $timestamp;

	/**
	 * Actions
	 * process => process
	 **/

	function __construct(object $data) {
		$this->data = $data;
		$this->results['status'] = false;
		$this->db = dBConnection(database: 'test_db');
		loadClass(className: 'InputValidator', parentDir: 'endpoints/helpers');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		$this->DBBridge = new DBBridge(db: $this->db);
		$this->timestamp = date('Y-m-d h:i:s');
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: [$this->db]);
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
		$action = method_exists(get_class($this), ($this->data->action??'noAction')) ? $this->data->action : 'inValidAction';
		return $this->$action();
	}

	/**
	 * Reset the results array
	 **/
	private function _resetResults(): void {
		$this->results = ['status' => false];
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


	/***********************************************
	 * Action implementations start here...
	 ***********************************************/

	/**
	 * Fetch system logs that matches a pattern
	 * */
	public function fetchMatchedDataLikeLogs(): object {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.fetchMatchedDataLikeLogs');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return (object)$this->results;
		}
		$sql = 'SELECT * FROM SystemLogs WHERE Data LIKE?';
		$bindTo = ['logId','title','action','data','timestamp'];
		$log = $this->DBBridge->fetch(sql: $sql, params: ["%{$this->data->dataLike}%"], bindTo: $bindTo);
		if($log->logId??false) {
			$log->data = json_decode($log->data);
		}
		return (object)$log;
	}

	/**
	 * Add a system log
	 **/
	public function add(): array {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.add');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
		$sql = 'INSERT INTO SystemLogs VALUES (?,?,?,?,?)';
		$params = [null,$this->data->title,$this->data->action,json_encode($this->data->data),$this->timestamp];
		$logId = $this->DBBridge->insert(sql: $sql, params: $params);
		$this->results['status'] = $logId !== false;
		$this->results['logId'] = $logId;
		return $this->results;
	}

	/**
	 * Delete a system log
	 **/
	public function delete(): array {
		return $this->results;
	}
}
?>
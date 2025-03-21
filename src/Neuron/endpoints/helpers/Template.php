<?php
/**
 * Endpoint: Template
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions is defined in each action method
 * Documentation: 
 **/
class Template {
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
		loadClass(className: 'Permissions', parentDir: 'endpoints/helpers');
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
		$Permissions = new Permissions(data: $this->data);
		$checked = (object)$Permissions->checkPermission();
		if(!$checked->status) {
			return (array)$checked;
		}
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
}
?>
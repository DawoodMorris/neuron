<?php
/**
 * Endpoint: Permissions
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions is defined in each action method
 * Documentation: 
 **/
class Permissions {
	//data for this endpoint to work with
	private object $data;

	//results of the the requested action on this endpoint
	private array $results = [];

	private object $db;
	private object $DBBridge;
	private array $_userPermissions;
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
	 * Reset the data object
	 * @param $data (object) The data object
	 **/
	public function _setData(object $data): void {
		$this->data = $data;
	}

	/**
	 * Fetch system permissions
	 **/
	private function _fetchUserPermissions(): array {
		//implementation here
		return [];
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
	 *Make sure that the permission/action Id we are getting is valid
	 **/
	private function _isValidPermissionId(): bool {
		//implementation here
		return true;
	}

	/**
	 * Check if given user has a given permission
	 * @param $permissionId (int) The user permission Id
	 * @param $userId (int) The user Id
	 **/
	private function _hasPermission(int $permissionId, int $userId): bool {
		//implementation here
		return true;
	}


	/***********************************************
	 * Action implementations start here...
	 ***********************************************/

	/**
	 * Assign system user permissions
	 **/
	private function saveUserSystemPermissions(): array {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.saveUserSystemPermissions');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
		//implement permissions logic here
		$this->results['status'] = true;
		$this->results['message'] = MESSAGES['successAssignPerms'];
		return $this->results;
	}

	/**
	 * Get the permissionId of a given action
	 **/
	public function getPermissionId(): int {
		//implementation here
		return 1;
	}

	/**
	 * Fetch available system permissions
	 **/
	private function viewAvailableSystemPermissions(): array {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.viewAvailableSystemPermissions');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
		//implement permissions logic here
		$this->results['status'] = true;
		return $this->results;
	}

	/**
	 * Check the user has permissions to perform the requested action.
	 **/
	public function checkPermission(): array {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.checkPermission');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
		if(!$this->_isValidPermissionId()) {
			$this->results['error'] = $error = 'invalidPermissionId';
			$this->results['message'] = MESSAGES[$error];
			return $this->results;
		}
		$this->_userPermissions = $this->_fetchUserPermissions();
		//implementation here
		$this->results['status'] = true;
		return $this->results;
	}
}
?>
<?php
/**
 * Endpoint: AccessToken
 * Contract: Manages the access token logic
 * Required Data: the acces token
 * Documentation: 
 **/
class AccessToken {
	//data for this endpoint to work with
	private stdClass $data;
	
	//database connection
	private object $db;
	private object $DBBridge;
	private string $timestamp;
	private string $_token;

	function __construct(stdClass $data) {
		$this->data = $data;
		$this->results['status'] = false;
		$this->db = @dBConnection(database: 'test_db');
		$this->data->accessToken = $this->data->accessToken??'';
		loadClass(className: 'InputValidator', parentDir: 'endpoints/helpers');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		$this->DBBridge = new DBBridge(db: $this->db);
		$this->timestamp = microtime(true);
		$this->_token = md5('sample_access_token');
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: [$this->db]);
	}

	/**
	 * Add an access token
	 **/
	public function add(): array {
		//implementation here
		return $this->results;
	}

	/**
	 * Retreive the access token given a user Id
	 **/
	public function retreiveAccessToken(): string {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.retreiveAccessToken');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
		return $this->_token;
	}

	/**
	 * Parse the access token into required token fields
	 **/
	public function parse(): object {
		//implementation here
		if(true) {
			return (object) [
				'userId' => 1,
				'userType' => 'endUser',
				'token' => $this->_token
			];
		}
		return (object) [
			'userId' => false,
			'userType' => false,
			'token' => false
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
}
?>
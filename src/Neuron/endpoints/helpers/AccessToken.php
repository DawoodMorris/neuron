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

	function __construct(stdClass $data) {
		$this->data = $data;
		$this->results['status'] = false;
		$this->db = @dBConnection(database: 'test_db');
		$this->data->accessToken = $this->data->accessToken??'';
		loadClass(className: 'InputValidator', parentDir: 'endpoints/helpers');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		$this->DBBridge = new DBBridge(db: $this->db);
		$this->timestamp = microtime(true);
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: [$this->db]);
	}

	/**
	 * Add an access token
	 **/
	public function add(): array {
		$sql = 'INSERT INTO AccessToken VALUES (?,?,?,?,?)';
		$params = [null,$this->data->userId,$this->data->userTypeId,$this->data->token,$this->timestamp];
		$added = $this->DBBridge->insert(sql: $sql, params: $params);
		$this->results['status'] = true;
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
		$sql = 'SELECT Token FROM AccessTokens WHERE UserId=?';
		$stmt = $this->db->prepare($sql);
		$stmt->bind_param('i',$this->data->userId);
		$stmt->execute();
		$stmt->bind_result($token);
		$stmt->fetch();
		$stmt->reset();
		return $token;
	}

	/**
	 * Parse the access token into required token fields
	 **/
	public function parse(): object {
		$sql = 'SELECT A_T.Token,A_T.UserId,UT.Id,UT.Type FROM AccessToken A_T INNER JOIN UserTypes UT ON UT.Id = A_T.UserTypeId WHERE A_T.Token=?';
		$bindTo = ['token','userId','userTypeId','userType'];
		$info = $this->DBBridge->fetch(sql: $sql, params: [$this->data->accessToken], bindTo: $bindTo);
		if($info->token??false) {
			return (object) [
				'userId' => $info->userId,
				'userType' => $info->userType,
				'userTypeId' => $info->userTypeId,
				'token' => $info->token
			];
		}
		return (object) [
			'userId' => false,
			'userType' => false,
			'userTypeId' => false,
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
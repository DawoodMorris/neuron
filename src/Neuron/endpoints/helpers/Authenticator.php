<?php
/**
** Bismillahir Rahmaanir Raheem
** Allah is the Creator and Master of the 
**/

/**
 * Endpoint: Authenticator
 * Contract: Athenticates users
 * Required Data: What this endpoint needs to complete the required actions
 * Documentation: 
 **/
class Authenticator {
	//data/config for this endpoint to work with
	private stdClass $data;

	//results of the the requested action on this endpoint
	private array $results = [];

	//database connection
	private object $db;

	private $currentTime;

	private int|null $loginId;
	private int|null $userId;
	private int|null $userTypeId;
	private object $DBBridge;

	private const SUPER_USER_KEY = 'NSKHDKDSLSJOWIWOWLJ';

	function __construct(stdClass $data) {
		$this->data = $data;
		$this->results['status'] = false;
		$this->db = @dBConnection(database: 'test_db');
		$this->currentTime = microtime(true);
		$this->loginId = 0;
		$this->userId = 0;
		$this->userTypeId = 0;
		loadClass(className: 'InputValidator', parentDir: 'endpoints/helpers');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		$this->DBBridge = new DBBridge(db: $this->db);
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
	 * Add login history
	 * @param (bool) $loginStatus Whther the login was a success or not
	 **/
	private function _recordLogin(bool $loginStatus): void {
		$sql = 'INSERT INTO LoginHistory VALUES (?,?,?,?)';
		$this->DBBridge->insert(sql: $sql, params: [null,$this->userId,$loginStatus,date('Y-m-d h:i:s')]);
	}

	/**
	 * Record login attemmpts
	 **/
	private function _recordFailedLoginAttempt(): void {
		$sql = 'INSERT INTO LoginFailedAttempt VALUES (?,?,?)';
		$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR']??$_SERVER['REMOTE_ADDR'];
		$data = json_encode(['username' => $this->data->username, 'password' => $this->data->password, 'ip' => $ipAddress]);
		$this->DBBridge->insert(sql: $sql, params: [null,$data,date('Y-m-d h:i:s')]);
	}

	/**
	 * Update token timestamp
	 **/
	private function _updateTokenTimestamp(string $token): void {
		$sql = 'UPDATE AccessToken SET TimeStamp=? WHERE Token=?';
		$this->DBBridge->update(sql: $sql, params: [$this->currentTime,$token]);
	}

	/**
	 * Fetch the user's access token
	 **/
	private function _accessToken(): string {
		$sql = 'SELECT Token FROM AccessToken WHERE Token=?';
		$accessToken =  $this->DBBridge->fetch(sql: $sql, params: [md5($this->data->username)], bindTo: ['token'])->token??false;
		if($accessToken) $this->_updateTokenTimestamp(token: $accessToken);
		return $accessToken;
	}

	/**
	 * Add access token for a given user. Information to be added is as follows:
	 * TokenId,Token,TypeId,UserId,TimeStamp. First we should check if a token does not exist and proceed 
	 * accordignly. If a token for this user exists, then we just update it to make it acccessible, otherwise we add 
	 * the token. If the current userId and the stored userId are not the same, we update the stored userId with the new userId.
	 **/
	private function addAccessToken(): void {
		$token = md5($this->data->username);
		//first check if token exists
		$sql = 'SELECT TokenId,UserId FROM AccessToken WHERE Token=?';
		$info = ($this->DBBridge->fetch(sql: $sql, params: [$token], bindTo: ['tokenId','userId']));
		if(empty($info->tokenId)) {
			$stmt->reset();
			$sql = 'INSERT INTO AccessToken (TokenId,Token,TypeId,UserId,TimeStamp) VALUES (?,?,?,?,?)';
			$stmt->bind_param('isiis',$id,$token,$this->userTypeId,$this->userId,$this->currentTime);
			$this->DBBridge->insert(sql: $sql, params: [null,$token,$this->userTypeId,$this->userId,$this->currentTime]);
		} else {
			if($info->userId !== $this->userId) {
				$stmt->reset();
				$sql = 'UPDATE AccessToken SET TimeStamp=?,UserId=? WHERE Token=?';
				$this->DBBridge->update(sql: $sql, params: [$this->currentTime,$this->userId,$token]);
			} else {
				$sql = 'UPDATE AccessToken SET TimeStamp=? WHERE Token=?';
				$this->DBBridge->update(sql: $sql, params: [$this->currentTime,$token]);
			}
		}
		$this->results['accessToken'] = $token;
	}

	/**
	 * Perform pre-authentications
	 * Check if the token exists and session has not expired
	 * @return $authResults (object) containing tokenExists,sessionExpired, and tokenData. 
	 * The property tokenExists is always a boolean and the property sessionExpired is the status 
	 * (boolean * [true|false] i.e expired or not expired respectively) 
	 * of the token session determined by its timestamp, 
	 * and the property tokenData is the token data (object) when tokenExists is true.
	 **/
	private function preAuthenticate(): object {
		$sql = 'SELECT AccessToken.*,UserTypes.Type FROM AccessToken INNER JOIN ';
		$sql .= 'UserTypes ON UserTypes.Id = AccessToken.UserTypeId WHERE AccessToken.Token=?';
		$authResults = ['tokenExists' => false,'sessionExpired' => true,'tokenData' => []];
		$stmt = $this->db->prepare($sql);
		$stmt->bind_param('s',$this->data->accessToken);
		$stmt->execute();
		$sqlResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0] ?? false;
		if($sqlResult && count($sqlResult) > 0) {
			if($this->getMinutes(prevTime: $sqlResult['Timestamp']) <= MAX_LOGIN_SESS_TIME) {
				$this->updateTokenTimeStamp();
				$authResults['tokenExists'] = true;
				$authResults['sessionExpired'] = false;
				$authResults['tokenData'] = $sqlResult;
				$this->_updateTokenTimestamp(token: $this->data->accessToken);
			} else {
				$authResults['tokenExists'] = true;
				$authResults['sessionExpired'] = true;
				$authResults['tokenData'] = $sqlResult;
			}
		}
		return (object)$authResults;
	}

	/**
	 * Calculate time (in minutes) elapsed since given previous time
	 * @param $prevTime (float) The Unix timestamp of the previous time to calculate from
	 * @return (float) Elapsed time in minutes
	 **/
	private function getMinutes(float $prevTime): float {
		$debugInfo = [];
		$debugInfo['context'] = 'getMinutes() function';
		$debugInfo['currentTime'] = $this->currentTime;
		$debugInfo['prevTime'] = $prevTime;
		$debugInfo['calculatedTimeMinutes'] = ($this->currentTime - $prevTime)/(60.00);
		debug($debugInfo);
		$theTime = ($this->currentTime-$prevTime)/60.0;
		return $theTime;
	}

	/**
	 * Update the access token time stamp to the current time, to prevent user session timeout 
	 * while logged in
	 */
	private function updateTokenTimeStamp(): void {
		$sql = 'UPDATE AccessToken SET TimeStamp=? WHERE `Token`=?';
		$stmt = $this->db->prepare($sql);
		$stmt->bind_param('ss',$this->currentTime,$this->data->accessToken);
		$stmt->execute();
		if($stmt->affected_rows > 0)
		{
			$this->results['tokenStatus'] = true;
			$this->results['sessionStatus'] = 'tokenUpdated';
		} else {
			$this->results['tokenStatus'] = false;
			$this->results['sessionStatus'] = 'tokenNotUpdated';
		}
	}

	/**
	 * Check the session timeout
	 **/
	private function checkSession(): array {
		$authResults = $this->preAuthenticate();
		if($authResults->tokenExists && !$authResults->sessionExpired) {
			$this->results['status'] = true;
			$this->results['message'] = MESSAGES['authSuccess'];
		} else if($authResults->tokenExists && $authResults->sessionExpired) {
			$this->results['status'] = false;
			$this->results['error'] = 'sessionExpired';
			$this->results['message'] = MESSAGES['sessionExpired'];
		} else {
			$this->results['status'] = false;
			$this->results['error'] = 'tokenDoesNotExist';
			$this->results['message'] = MESSAGES['tokenDoesNotExist'];
		}
		return $this->results;
	}

	/**
	 * Timout the session
	 **/
	private function logout(): array {
		return $this->outdateTokenTimeStamp();
	}

	/**
	 * Outdate the access token
	 **/
	private function outdateTokenTimeStamp(): array {
		$sql = 'UPDATE AccessToken SET TimeStamp=? WHERE Token=?';
		$stmt = $this->db->prepare($sql);
		$timestamp = 0.0;
		$stmt->bind_param('ds',$timestamp,$this->data->accessToken);
		$stmt->execute();
		$pattern = '/Rows matched: 1/';
		if($stmt->affected_rows > 0 || preg_match($pattern,$this->db->info??'')) {
			$this->results['status'] = true;
			$this->results['sessionStatus'] = 'tokenOutdated';
		} else {
			$this->results['sessionStatus'] = 'tokenNotUpdated';
		}
		return $this->results;
	}

	/**
	 * This is like a login but for an existing access token
	 **/
	private function updateSession(): array {
		$this->updateTokenTimeStamp();
		return $this->results;
	}

	/**
	 * Add a super user
	 **/
	private function addSuperUser(): array {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.'.$this->data->action);
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return (array)$this->results;
		}
		if($this->data->superUserKey !== Authenticator::SUPER_USER_KEY) {
			$error = 'invalidSuperUserKey';
			$this->results['error'] = $error;
			$this->results['message'] = MESSAGES[$error];
		}
		loadClass(className: 'User', parentDir: 'endpoints/helpers');
		$User = new User(data: $this->data);
		return $User->addSuperUser();
	}

	/**
	 * Login a user. The user may have two login usernames, here referred to as Username1 or Username2
	 * in the database, make sure to lookup them both.
	 **/
	private function login(): array {
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.login');
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return (array)$this->results;
		}
		$sql = 'SELECT Id AS LoginId,UserId,Password FROM Login WHERE (Username1=? OR Username2=?) AND Status=?';
		$active = 1;
		$info = $this->DBBridge->fetch(sql: $sql, params: [$this->data->username,$this->data->username,$active], bindTo: ['loginId','userId','password']);
		$this->loginId = $info->loginId??false;
		$this->userId = $info->userId??false;
		if(($this->loginId) && ($this->userId)) {
			if(password_verify($this->data->password, $info->password)) {
				$this->results['status'] = true;
				$this->results['message'] = MESSAGES['loginSuccess'];
				$this->results['accessToken'] = $this->_accessToken();
				$this->_recordLogin(loginStatus: true);
			} else {
				$this->results['error'] = 'incorrectCreds';
				$this->results['message'] = MESSAGES['incorrectCreds'];
				$this->_recordLogin(loginStatus: false);
			}
		} else {
			//check if Account is not suspended
			$sql = 'SELECT UserId,Status FROM Login WHERE (Username1=? OR Username2=?)';
			$info = $this->DBBridge->fetch(sql: $sql, params: [$this->data->username,$this->data->username], bindTo: ['userId','status']);
			$this->userId = $info->userId??false;
			if(($this->userId)) {
				$this->_recordLogin(loginStatus: false);
				if(!$info->status) {
					$this->results['error'] = 'accountSuspended';
					$this->results['message'] = MESSAGES['accountSuspended'];
				}
			} else {
				$this->results['error'] = 'doesNotExist';
				$this->results['message'] = MESSAGES['doesNotExist'];
				$this->_recordFailedLoginAttempt();
			}
		}
		return $this->results;
	}

	/**
	 * Action implementations start here...
	 **/

	/**
	 * Authenticate a request,...etc
	 **/
	public function authenticate(): array {
		$authResults = $this->preAuthenticate();
		if($authResults->tokenExists && !$authResults->sessionExpired) {
			$this->results['status'] = true;
			$this->results['message'] = MESSAGES['authSuccess'];
		} else if($authResults->tokenExists && $authResults->sessionExpired) {
			$this->results['status'] = false;
			$this->results['error'] = 'sessionExpired';
			$this->results['message'] = MESSAGES['sessionExpired'];
			$this->results['preAuthenticate'] = $authResults;
		} else {
			$this->results['status'] = false;
			$this->results['error'] = 'tokenDoesNotExist';
			$this->results['message'] = MESSAGES['tokenDoesNotExist'];
		}
		return $this->results;
	}
}
?>
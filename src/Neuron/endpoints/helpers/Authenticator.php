<?php
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
		//implementation here
	}

	/**
	 * Record login attemmpts
	 **/
	private function _recordFailedLoginAttempt(): void {
		//implementation here
	}

	/**
	 * Update token timestamp
	 **/
	private function _updateTokenTimestamp(string $token): void {
		//implementation here
	}

	/**
	 * Fetch the user's access token
	 **/
	private function _accessToken(): string {
		//implementation here
		return '';
	}

	/**
	 * Add access token for a given user. Information to be added is as follows:
	 * TokenId,Token,TypeId,UserId,TimeStamp. First we should check if a token does not exist and proceed 
	 * accordignly. If a token for this user exists, then we just update it to make it acccessible, otherwise we add 
	 * the token. If the current userId and the stored userId are not the same, we update the stored userId with the new userId.
	 **/
	private function addAccessToken(): void {
		//implementation here
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
		//implementation here
		return new stdClass;
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
		//implementation here
	}

	/**
	 * Check the session timeout
	 **/
	private function checkSession(): array {
		//implementation here
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
		//implementation here
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
		//implementation here
		return [];
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
		//implementation here
		return $this->results;
	}

	/**
	 * Action implementations start here...
	 **/

	/**
	 * Authenticate a request,...etc
	 **/
	public function authenticate(): array {
		//implementation here
		return $this->results;
	}
}
?>
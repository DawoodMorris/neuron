<?php
/**
 * Endpoint: Auth
 * Contract: Manages authentication
 * Required Data: Specified in the action methods
 **/
class Auth {

	//payload for this endpoint to work with
	private object $payload;

	//results of the the requested action on this endpoint
	private array $results = [];

	function __construct(object $payload) {
		$this->payload = $payload;
		$this->results['status'] = false;
		loadClass(className: 'Authenticator', parentDir: 'endpoints/helpers');
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
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
	 * Perform an authentication action
	 * */
	private function authenticate(): array {
		//implementation here
		return $this->results;
	}

	/**
	 * Check the session timeout
	 * */
	private function checkSession(): array {
		//implementation here
		return $this->results;
	}

	/**
	 * Timout the session/logout
	 * */
	private function logoutUser(): array {
		//implementation here
		return $this->results;
	}

	/**
	 * Login a user...
	 **/
	private function login(): array {
		//implementation here
		return $this->results;
	}

	/**
	 * Get access token details
	 **/
	private function getAccessToken(): array {
		//implementation here
		return $this->results;
	}
}
?>
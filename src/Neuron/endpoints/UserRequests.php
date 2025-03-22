<?php
/**
 * It is recommended to channel all requests to this endpoint
 * Endpoint: UserRequests
 * Contract: Manages all UserRequests on the platform. Some PublicRequests are unauthenticated. 
 * Read more here 
 * Required Data: The payload with respective data defined in the actions/methods
 **/
class UserRequests extends Authenticator {

	//payload for this endpoint to work with
	private object $payload;

	private array $results = [];

	private string $timestamp;
	private string $today;

	//Bundled request handlers must be defined here
	private const BUNDLED_REQUEST_HANDLERS = [
	];
	private const TOKEN_TYPES_AUTHORIZED = [];

	//A parsed token from the access token
	private object $parsedToken;

	function __construct(object $payload) {
		$this->payload = $payload;
		$this->results['status'] = false;
		loadClass(className: 'AccessToken', parentDir: 'endpoints/helpers');
		loadClass(className: 'InputValidator', parentDir: 'endpoints/helpers');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		$AccessToken = new AccessToken(data: (object)$this->payload->data);
		$this->parsedToken = $AccessToken->parse();
		$data = (object)[
			'accessToken' => $this->payload->data->accessToken??''
		];
		parent::__construct($data);
		$this->timestamp = microtime(true);
		$this->today = date('Y-m-d h-i-s');
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: []);
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
		$authResults = (object)parent::authenticate();
		if(isset($authResults->status) && $authResults->status) {
			$action = method_exists(get_class($this), ($this->payload->action??'noAction')) ? $this->payload->action : 'inValidAction';
			$this->results = $this->$action();
		} else {
			if($this->payload->data->publicRequest??false) {
				$action = method_exists(get_class($this), ($this->payload->action??'noAction')) ? $this->payload->action : 'inValidAction';
				$this->results = $this->$action();
			} else {
				$this->results = (array)$authResults;
			}
		}
		return $this->results;
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

	/*******************************************************************************************
	 * Action implementations start here...
	 *******************************************************************************************/

	/**
	 * Fetch quick statistics
	 **/
	private function fetchQuickStats(): array {
		loadClass(className: 'QuickStats', parentDir: 'endpoints/helpers');
		$this->payload->data->action = $this->payload->action;
		$QuickStats = new QuickStats(data: $this->payload->data);
		return $QuickStats->process();
	}


	/**
	 * Fetch quick help content
	 **/
	private function fetchQuickHelpContent(): array {
		return $this->results;
	}
	
}
?>
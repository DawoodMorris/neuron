<?php
/**
 * Endpoint: Template
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions
 * DocumentationURL: 
 **/
class Template {

	//payload for this endpoint to work with
	private object $payload;

	//results of the the requested action on this endpoint
	private array $results = [];

	private object $db;
	private object $DBBridge;

	/**
	 * Actions
	 * process => process
	 **/

	function __construct(object $payload) {
		$this->payload = $payload;
		$this->results['status'] = false;
		$this->db = @dBConnection(database: 'test_db');
		loadClass(className: 'AccessToken', parentDir: 'endpoints/helpers');
		loadClass(className: 'DBBridge', parentDir: 'endpoints/helpers');
		$AccessToken = new AccessToken(data: $payload->data);
		$this->parsedToken = $AccessToken->parse();
		$data = (object)[
			'accessToken' => $this->payload->data->accessToken
		];
		parent::__construct($data);
		$this->DBBridge = new DBBridge(db: $this->db);
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: [$this->db]);
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

	/******************************
	 * Action implementations start here...
	 *****************************/
}
?>
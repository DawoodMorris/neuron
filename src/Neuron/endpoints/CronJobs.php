<?php
/**
 * Endpoint: CronJobs
 * Contract: The contract of this endpoint here...
 * Required Data: What this endpoint needs to complete the required actions
 **/
class CronJobs {

	//payload for this endpoint to work with
	private object $payload;

	//results of the the requested action on this endpoint
	private array $results = [];

	//endpoint actions
	private const ACTIONS = [
	];

	function __construct(object $payload) {
		$this->payload = $payload;
		$this->results['status'] = false;
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
		$action = CronJobs::ACTIONS[$this->payload->action] ?? 'inValidAction';
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
	 * Action implementations start here...
	 **/
}
?>
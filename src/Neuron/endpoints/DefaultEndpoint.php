<?php
/**
 * Endpoint: DefaultEndpoint
 * Contract: This DefaultEndpoint is called when the specified endpoint could not be loaded 
 * due to the fact that it does not exist/not implemented yet
 * Required Data: What this endpoint needs to complete the required actions
 **/
class DefaultEndpoint {

	//payload for this endpoint to work with
	private object $payload;

	//results of the the requested action on this endpoint
	private $results = [];
	private const ACTIONS = [
		'default' => 'default'
	];

	function __construct(object $payload) {
		$this->payload = $payload;
		$this->results['status'] = false;
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
		$action = 'default';
		return $this->$action();
	}

	/**
	 * Call default action
	 **/
	private function default(): array {
		return [
			'status' => false,
			'error' => 'endpointNotFound',
			'errorMessage' => MESSAGES['endpointNotFound']
		];
	}
}
?>
<?php


/**
 * Validator: UserRequests
 * Contract: Validates UserRequests action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: https://docs.techxdyanmics/docs/wms/validators/UserRequests
 * Note: class name must be $UserRequestsValidator, e.g UserRequestsValidator
 * */
class UserRequestsValidator {
	private object $data;
	private object $results;
	function __construct(object $data) {
		$this->data = $data;
		$this->results = new stdClass;
		$this->results->valid = false;
		$this->results->error = 'invalidInput';
	}


	/**
	 * Invalid validator action supplied
	 * @return (object) The results
	 **/
	public function invalidValidatorAction(): object {
		$this->results->error = 'invalidValidatorAction';
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function sampleAction(): object {
		if(!($this->data->someArg??false)) {
			$this->results->error = 'missingSomeArg';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function beginPasswordResetChallenge(): object {
		if(!($this->data->email??false)) {
			$this->results->error = 'missingEmail';
			return $this->results;
		}
		if(!(isValidEmail(email: $this->data->email))) {
			$this->results->error = 'invalidInputEmail';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function bundledRequest(): object {
		if(!($this->data->requests??false)) {
			$this->results->error = 'missingRequests';
			return $this->results;
		}
		if(!(gettype($this->data->requests) === 'array')) {
			$this->results->error = 'invalidInputRequests';
			return $this->results;
		}
		if(count($this->data->requests) < 1) {
			$this->results->error = 'giveAtLeastOneRequest';
			return $this->results;
		}
		foreach($this->data->requests as $request) {
			if(!($request->action??false)) {
				$this->results->error = 'noValidRequestAction';
				return $this->results;
			}
		}
		foreach($this->data->requests as $request) {
			if(!($request->data??false)) {
				$this->results->error = 'noValidRequestData';
				return $this->results;
			}
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
<?php
/**
 * Validator: AccessToken
 * Contract: Validates AccessToken action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: https://docs.techxdyanmics/docs/wms/validators/AccessToken
 * Note: class name must be $AccessTokenValidator, e.g AccessTokenValidator
 * */
class AccessTokenValidator {
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
	public function retreiveAccessToken(): object {
		if(!($this->data->userId??false)) {
			$this->results->error = 'missingUserId';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
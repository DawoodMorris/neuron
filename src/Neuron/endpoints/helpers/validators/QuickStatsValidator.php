<?php

/**
 * Validator: QuickStats
 * Contract: Validates QuickStats action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: 
 * Note: class name must be $QuickStatsValidator, e.g QuickStatsValidator
 * */
class QuickStatsValidator {
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
		if(!($this->data->permissionId??false)) {
			$this->results->error = 'missingPermissionId';
			return $this->results;
		}
		if(!($this->data->parsedToken??false)) {
			$this->results->error = 'missingParsedToken';
			return $this->results;
		}
		if(!($this->data->action??false)) {
			$this->results->error = 'missingAction';
			return $this->results;
		}
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
	public function fetchQuickStats(): object {
		//if the action expects input, validate it here
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
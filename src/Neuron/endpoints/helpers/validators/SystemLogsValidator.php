<?php

/**
 * Validator: SystemLogs
 * Contract: Validates SystemLogs action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: https://docs.techxdyanmics/docs/wms/validators/SystemLogs
 * Note: class name must be $SystemLogsValidator, e.g SystemLogsValidator
 * */
class SystemLogsValidator {
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
	public function add(): object {
		if(!($this->data->title??false)) {
			$this->results->error = 'missingLogTitle';
			return $this->results;
		}
		if(!($this->data->action??false)) {
			$this->results->error = 'missingAction';
			return $this->results;
		}
		if(!($this->data->data??false)) {
			$this->results->error = 'mssingLogData';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function fetchMatchedDataLikeLogs(): object {
		if(!($this->data->dataLike??false)) {
			$this->results->error = 'missingDataLike';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
<?php

/**
 * Validator: Login
 * Contract: Validates Login action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: https://docs.techxdyanmics/docs/wms/validators/Login
 * Note: class name must be $LoginValidator, e.g LoginValidator
 * */
class LoginValidator {
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
	public function addLoginsToInstitution(): object {
		$maxNoOfLogins = 2000;
		if(!($this->data->username??false)) {
			$this->results->error = 'missingUsername';
			return $this->results;
		}
		if(!($this->data->password??false)) {
			$this->results->error = 'missingPassword';
			return $this->results;
		}
		if(!($this->data->nofLogins??false)) {
			$this->results->error = 'missingNoOfLogins';
			return $this->results;
		}
		if(!($this->data->instituteId??false)) {
			$this->results->error = 'missingInstituteId';
			return $this->results;
		}
		if(!(isset($this->data->tllOption))) {
			$this->results->error = 'missingTLLOption';
			return $this->results;
		}
		if(!(gettype($this->data->nofLogins) === 'integer')) {
			$this->results->error = 'invalidInputTypeIntegerNOFL';
			return $this->results;
		}
		if(!(gettype($this->data->instituteId) === 'integer')) {
			$this->results->error = 'invalidInputInstituteId';
			return $this->results;
		}
		if(!(gettype($this->data->tllOption) === 'boolean')) {
			$this->results->error = 'invalidInputTypeBool';
			return $this->results;
		}
		if($this->data->nofLogins > $maxNoOfLogins) {
			$this->results->error = 'loginsTooManyMax2000';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function checkExistingBaseUsername(): object {
		if(!($this->data->username??false)) {
			$this->results->error = 'missingUsername';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
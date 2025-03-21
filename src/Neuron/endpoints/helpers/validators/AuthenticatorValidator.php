<?php

/**
 * Validator: Authenticator
 * Contract: Validates Authenticator action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: 
 * Note: class name must be $AuthenticatorValidator, e.g AuthenticatorValidator
 * */
class AuthenticatorValidator {
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
	public function addSuperUser(): object {
		if(!($this->data->superUserKey??false)) {
			$this->results->error = 'missingSuperUserKey';
			return $this->results;
		}
		if(!($this->data->email??false)) {
			$this->results->error = 'missingEmail';
			return $this->results;
		}
		if(!($this->data->password??false)) {
			$this->results->error = 'missingPassword';
			return $this->results;
		}
		if(!($this->data->firstname??false)) {
			$this->results->error = 'missingFirstname';
			return $this->results;
		}
		if(!($this->data->lastname??false)) {
			$this->results->error = 'missingLastname';
			return $this->results;
		}
		if(!($this->data->userTypeId??false)) {
			$this->results->error = 'missingUserTypeId';
			return $this->results;
		}
		if(!($this->data->cellphone??false)) {
			$this->results->error = 'missingCellphone';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function login(): object {
		if(!($this->data->username??false)) {
			$this->results->error = 'missingUsername';
			return $this->results;
		}
		if(!($this->data->password??false)) {
			$this->results->error = 'missingPassword';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
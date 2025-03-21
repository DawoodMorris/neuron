<?php

/**
 * Input Validator.
 * Loads and runs a given validator
 **/
class InputValidator {
	private object $data;
	private object $results;

	function __construct(object $data) {
		$this->data = $data;
		$this->results = new stdClass;
		$this->results->valid = false;
		$this->results->error = 'invalidInput';
	}

	/**
	 * Validate input for a given action
	 * @param $action (string) The action to validate input for
	 **/
	public function validate(string $action): object {
		$parts = explode('.', $action);
		$action = $parts[1]??'invalidValidatorAction';
		$Validator = $parts[0]??'InvalidValidator';
		loadClass(className: $Validator, parentDir: 'endpoints/helpers/validators');
		$Validator = new $Validator(data: $this->data);
		if(method_exists($Validator, $action)) {
			return $Validator->$action();
		}
		return $Validator->invalidValidatorAction();
	}
}
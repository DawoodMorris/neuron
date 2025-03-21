<?php


/**
 * Validator: QuickHelpContent
 * Contract: Validates QuickHelpContent action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: https://docs.techxdyanmics/docs/wms/validators/QuickHelpContent
 * Note: class name must be $QuickHelpContentValidator, e.g QuickHelpContentValidator
 * */
class QuickHelpContentValidator {
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
	public function fetchQuickHelpContent(): object {
		if(!($this->data->section??false)) {
			$this->results->error = 'missingDocsSection';
			return $this->results;
		}
		if(!($this->data->topic??false)) {
			$this->results->error = 'missingTopic';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
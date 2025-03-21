<?php

/**
 * Validator: Permissions
 * Contract: Validates Permissions action inputs
 * Required Data: What this validators expected to complete the required validation is defined in each validator method
 * Documentation: 
 * Note: class name must be $PermissionsValidator, e.g PermissionsValidator
 * */
class PermissionsValidator {
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
	public function saveUserSystemPermissions(): object {
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
		if(!($this->data->staffId??false)) {
			$this->results->error = 'missingStaffId';
			return $this->results;
		}
		if(!($this->data->permissions??false)) {
			$this->results->error = 'missingPermissions';
			return $this->results;
		}
		if(!($this->data->deletePermissions??false)) {
			$this->results->error = 'missingDeletePermissions';
			return $this->results;
		}
		if(!(gettype($this->data->permissions) === 'array')) {
			$this->results->error = 'invalidInputTypeArray.Permissions';
			return $this->results;
		}
		if(!(gettype($this->data->deletePermissions) === 'array')) {
			$this->results->error = 'invalidInputTypeArray.DeletePermissions';
			return $this->results;
		}
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function viewAvailableSystemPermissions(): object {
		if(!($this->data->staffId??false)) {
			$this->results->error = 'missingStaffId';
			return $this->results;
		}
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
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}

	/**
	 * Validate that this action gets the required input
	 **/
	public function checkPermission(): object {
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
		$this->results->valid = true;
		$this->results->error = false;
		return $this->results;
	}
}
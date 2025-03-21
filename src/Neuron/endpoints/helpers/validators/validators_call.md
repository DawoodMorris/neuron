Each validator action returns an object with these props:

`status` with value `true` if the validation passes and `false` otherwise.
`error` The error when the validation `status` is `false`.


# Call a given validator like this:
`
		$InputValidator = new InputValidator(data: $this->data);
		$inputValidity = $InputValidator->validate(action: get_class($this).'Validator.'.$this->data->action);
		if(!($inputValidity->valid)) {
			$this->results['error'] = $inputValidity->error;
			$this->results['message'] = MESSAGES[$inputValidity->error];
			return $this->results;
		}
`
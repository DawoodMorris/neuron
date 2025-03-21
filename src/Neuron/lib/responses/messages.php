<?php
/**
 * Extend this array with relevant errors and their messages
 * Neoron and Endpoint error messages and success messages associated to their errors.
 **/
$messages = [
	'invalidInputTypeObject.Info' => 'Error: invalid input type for `info`, expecting type object.',
	'missingInfo.Test' => 'Error: missing required input `info` (object).',
	'invalidInputObject.Payload.Data' => 'Error: invalid input `data` on object `payload`, expecting type object.',
	'missingPayload.Data' => 'Error: missing required input `data` (object) on object `payload`.',
	'missingPayload.Action' => 'Error: missing required input `action` (string) on object `payload`.',
	'invalidInputObject.Payload' => 'Error: invalid input `payload` on the request body object, expecting type object.',
	'missingPayload' => 'Error: missing required input `payload` (object).',
	'missingEndpoint' => 'Error: missing required input `endpoint` (string).',
	'pingSuccess' => 'Sucess: all good. You are a rock star!',
	'serverError' => 'Error: the server encountered an error and could not complete the request.',
	'endpointNotFound' => 'Error: the specified endpoint is not implemented.',
	'inValidAction' => 'Error: the specified action is not implemented.',
	'invalidInputForNeuron' => 'Error: invalid input, expecting JSON data format.',
	'apiKeyAuthError' => 'Error: failed to authenticate your API key.'
];

?>

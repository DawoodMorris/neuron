<?php

/**
 * Test endpoint tests
 **/

/**
 * Test: Test that the Test endpoint responds with status true for a ping 
 * Expected: The response must be a JSON object as follows: {"data":{"status":"true"}}
 * Pass: When data.status is true
 * Fail: When data.status is false or any other outcome other than {"data":{"status":"true"}}
 * Payload: A paylod for the test to be sent to the server. Make sure to use test data! Sample paload looks like this:
 *  {"endpoint":"Test","payload":{"action":"ping","data":[]}}. the payload.data object has the necessary data as 
 * arguments for the test
 **/
function test(): bool {
	$payload = [
		'endpoint' => 'Test',
		'payload' => [
			'action' => 'ping',
			'data' => [
				
			]
		]
	];
	$_payload = json_encode($payload);
	try {
		$results = makeApiRequest(url: NERVOUS_URL, headers: HEADERS, payload: $payload);
		$resultsJSON = json_decode($results);
		if(!$resultsJSON) {
			errorOnParseJSON(_payload: $_payload, results: $results);
			return false;
		}
		if(isset($resultsJSON->data) && $resultsJSON->data->status) return true;
		errorOnEndpointRequest(_payload: $_payload, results: json_encode($resultsJSON));
		return false;
	} catch (Exception $e) {
		errorException(_payload: $_payload, results: $e);
		return false;
	}
}
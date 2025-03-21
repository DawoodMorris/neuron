<?php

/**
 * Template endpoint tests
 **/

/**
 * Test: Test that the Nervous server responds with status true for a ping
 * Expected: The response must be a JSON object as follows: {"data":{"status":"true"}}
 * Pass: When data.status is true
 * Fail: When data.status is false or any other outcome other than {"data":{"status":"true"}}
 * Payload: A paylod for the test to be sent to the server. Make sure to use test data! Sample paload looks like this:
 *  {"endpoint":"endpoint","payload":{"action":"action","data":[]}}. the payload.data object has the necessary data as 
 * arguments for the test
 **/
function templateTestFunction(): bool {
	$payload = [
		'endpoint' => 'Endpoint',
		'payload' => [
			'action' => 'action',
			'data' => [

			]
		]
	];
	$_payload = json_encode($payload);
	try {
		$results = makeApiRequest(url: NERVOUS_URL, headers: HEADERS, payload: $payload);
		$resultsJSON = json_decode($results);
		if(!$resultsJSON) {
			print "Error: \n";
			print_r($results);
			print "\n";
			print "Test was called with payload: {$_payload}\n";
			print "\n";
			return false;
		}
		if(isset($resultsJSON->data) && $resultsJSON->data->status) return true;
		print "Error: \n";
		print_r($resultsJSON);
		print "\n";
		print "Test was called with payload: {$_payload}\n";
		print "\n";
		return false;
	} catch (Exception $e) {
		print "Exception: \n";
		print_r($e);
		print "\n";
		print "Test was called with payload: {$_payload}\n";
		print "\n";
		return false;
	}
}
<?php

/**
 * Test Helper functions
 **/

define('HEADERS', 'APIKEY: kdjshsLSJE-JmjJNDJMFHMWFHWEHJDD#@$eieowlsldcnwe,Cache-Control: no-cache');

$local = $argv[2]??$argv[1]??false;
if($argv && $local) {
    if($local === '--local') {
        print "\n********** Using Local Server: http://localhost:8000/serve/ **********\n\n";
        define('NERVOUS_URL', 'http://localhost:8000/serve/');
    } else {
        define('NERVOUS_URL', 'https://api.computers4kids.co.za/serve/');
    }
}

/**
 * Performs an http call to an api end point
 * This to be used internally
 * @param $url (string) The url end point to make the request call
 * @param $headers (string) HTTP Headers, comman separted key:value pairs
 * @param $payload (array) The request payload to send
 * @return string of the response
 **/
function makeApiRequest(string $url,string $headers, array $payload): string {
    $_payload = json_encode($payload);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
   	curl_setopt($ch,CURLOPT_POSTFIELDS, $_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, explode(',', $headers));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) {
    	return json_encode(['data' => (object)['status' => false, 'error' => 'curlLibError','message' => $error]]);
    }
    return curl_exec($ch);
}


/**
 * Print some error
 * @param $_payload (string) The payload
 * @param $results (string) The results
 **/
function errorOnParseJSON(string $_payload, string $results): void {
    print "Error: \n";
    print_r($results);
    print "\n";
    print "Test was called with payload: {$_payload}\n";
    print "\n";
}


/**
 * Print some error
 * @param $_payload (string) The payload
 * @param $results (string) The results
 **/
function errorOnEndpointRequest(string $_payload, string $results): void {
    print "Error: \n";
    print_r($results);
    print "\n";
    print "Test was called with payload: {$_payload}\n";
    print "\n";
}


/**
 * Print some error
 * @param $_payload (string) The payload
 * @param $e (string) The exception
 **/
function errorException(string $_payload, string $e): void {
    print "Exception: \n";
    print_r($e);
    print "\n";
    print "Test was called with payload: {$_payload}\n";
    print "\n";
}



?>
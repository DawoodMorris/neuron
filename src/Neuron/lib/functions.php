<?php
/**
** Dawood Morris Kaundama
** Jun 2021 09:16:22 SAST
**/

//Load constants
require 'constants.functions.php';
require 'constants.php';
require NEURON_ROOT.'lib/DatabaseConnector.php';

/**
 * The builtin print customised for printing CONSTANT variables in the HEREDOC content
 * @param $msg (string) The message argument
 **/
function _print(string $msg): void {
	print "{$msg}".NL;
}

/**
 * Extract a link from a given HTML document
 * @param $htmlDoc (string) The html doc
 **/
function extractLinkFromHTMLDocument(string $htmlDoc): string {
	// Load the HTML into a DOMDocument object
	$doc = new DOMDocument();
	libxml_use_internal_errors(true); // Suppress errors due to invalid HTML
	$doc->loadHTML($htmlDoc);
	libxml_clear_errors();
	// Create an XPath object to query the DOM
	$xpath = new DOMXPath($doc);
	// Extract the URL from the <a> tag
	$aTag = $xpath->query('//a')->item(0);
	if ($aTag) {
	    $aUrl = $aTag->getAttribute('href');
	    return $aUrl;
	}
	return '';
}


/**
 * Pad a given string with a given character/string to padd the string with. The padding is done between each character of the string.
 * @param $str (string) The string to do the padding in
 * @param $padWith (string) The string to pad the string with, i.e ' ' - which is a space. Default is space.
 * @param padLength (int) The length of the padd to perfom/repeat. For example 3 pads $padWith 3 times each time it is inserted.
 * Default is 1.
 * @return $str (string) The padded string
 **/
function padStringInterCharacter(string $str, string $padWith = ' ', int $padLength = 1): string {
	if(!($str) || (gettype($str) !== 'string')) throw new Exception("[padStringInterCharacter] Error: Invalid input `str` - expecting a string, but given: {$str}", 1);
	$strArr = str_split($str);
	$_padWith = '';
	for ($i=0; $i < $padLength; $i++) { 
		$_padWith .= $padWith;
	}
	$paddedStr = '';
	foreach($strArr as $_char) {
		$paddedStr .= "{$_char}{$_padWith}";
	}
	return $paddedStr;
}


/**
 * Check whether we are running on the dev server
 **/
function isOnDevServer(): bool {
	if(preg_match('/staging./',NEURON_ROOT)) return true;
	if(preg_match('/eng./',NEURON_ROOT)) return true;
	if(!preg_match('/service/',NEURON_ROOT)) return true;
	return false;
}

/**
 * Validate if a given cellphone is a valid cellphone (only contains digits)
 * @param $cellphone (string) The cellphone to check
 **/
function isValidCellphone(string $cellphone): bool {
	return preg_match('/^\d+$/', $cellphone);
}



/**
 * Respond when NeuronF receives invalid input
 **/
function messageInputError(): array {
	return [
		'data' => [
			'status' => false,
			'error' => 'invalidInputForNeuron',
			'message' => MESSAGES['invalidInputForNeuron']
		]
	];
}

/**
 * When API Key Authentication fails
 **/
function messageAuthError(): array {
	return [
		'data' => [
			'status' => false,
			'error' => 'apiKeyAuthError',
			'errorMessage' => MESSAGES['apiKeyAuthError'],
			'date' => date('Y-m-d h:i:sa'),
			'requestOrigin' => $_SERVER['X_FORWARDED_FOR']??$_SERVER['REMOTE_ADDR']
		]
	];
}


/**
 * @author Dawood Morris Kaundama
 * Try to remove unwanted chars from web unser input
 * It may help neutralize theuser input from malicous symbols
 * Asymptotic Analysis of the algorithm is T(n) = O(28n) + the T(n) 
 * of the implode PHP built function in the worst case, 
 * where n = length of input string and
 * 23 is a contant representing the size of the special symbols
 * Since in real sense, the user input string length might not be longer than 100 chars. 
 * At least I assume
 * call with flag =true when sanitizing an email address since it has legal characters @ and .
 * @param $user_input (string) from internet users
 * @param flag (boolean) if email
 * @return sanitized user input
 **/
function sanitizeInput(string $user_input, bool $flag=false): string|bool {
	if(!$user_input) return false;
	if($flag)
	{
		$suspicious_chars = array(
			"~","`","!","#","$","%","^","&",
			"*","(",")","?",",","{","}",
			"[","]","\\","|",">","<","/",";",
			":","\"");
	}
	else
	{
		$suspicious_chars = array(
			"~","`","!","@","#","$","%","^","&",
			"*","(",")","-","?",",",".","{","}",
			"[","]","\\","|",">","<","/",";",":",
			"\"","_","+");
	}
	
	$splitted_haystack = str_split($user_input);
	$to_return_haystack = [];
	foreach ($splitted_haystack as $i => $char) {
		if(!in_array($char, $suspicious_chars)) array_push($to_return_haystack, $char);
	}
	return implode("", $to_return_haystack);
}


/**
 * @param $sk (string) the C4K API token
 **/
function verifyAPISK(string $sk): bool {
	return password_verify($sk, password_hash(API_SK, PASSWORD_DEFAULT));
}

/**
 * @param $key (string) Key supplied by the user/client
 * @return boolean
 **/
function authenticateAPISKey(string $key): bool {
	return password_verify($key, password_hash(getAPIKey(), PASSWORD_DEFAULT));
}

/**
 * Get an API key
 **/
function getAPIKey(): string {
	return getenv('API_KEY');
}

/**
 * Database connections
 **/

/**
 * @param $database (string) The name of the database object to use
 * @return (object|boolean) database connection to $database when $database definition exists at the given host
 * false otherwise
 **/
function dBConnection(string $database): object|bool {
	$database = strtolower($database);
	$DatabaseConnector = new DatabaseConnector($database);
	return $DatabaseConnector->connect();
}

/**
 * Performs an http call to an api end point
 * This to be used internally
 * @param $url (string) The url end point to make the request call
 * @param $requestPayload (array) The request payload to send
 * @param $headers (string) HTTP Headers, comman separted key:value pairs
 * @return JSON object of the response
 **/
function makeApiRequest(string $url,string $headers, array $requestPayload): object {
    $queryPayload = json_encode($requestPayload);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
   	curl_setopt($ch,CURLOPT_POSTFIELDS, $queryPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, explode(',', $headers));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) {
    	return (object)['data' => (object)['status' => false, 'error' => $error]];
    }
    $outcome = curl_exec($ch);
    $_outcome = json_decode($outcome);
   	if(!$_outcome) {
   		return (object)['data' => (object)['status' => false, 'error' => $outcome]];
   	}
    return (object)$_outcome;
}


/**
 * This function does some logging of debugging information to a file 
 * @param $data The data to log. It is an assoiciative arrary of information
 **/
function debug(array $data): void {
	if(is_array($data)) {
		if(!defined('NL')) {
			define('NL',"\n");
		}
		$str = '---------------------------------------------------'.NL;
		$str .= 'LogTimeStamp : '.date('Y-m-d h-i-sa').NL;
		foreach ($data as $key => $value) {
			if(is_array($value)) {
				$str .= $key.' values: '.NL;
				foreach ($value as $key => $value) {
					$str .= $key.' : '.$value.NL;
				}
			} else {
				$str .= $key.' : '.$value.NL;
			}
		}
		$str .= '---------------------------------------------------'.NL;
		$logFile = fopen(LOG_FILE, 'a');
		fwrite($logFile, $str);
		fclose($logFile);
	}
}

/**
 * Assumes the given cell phone number is valid
 * Removes the leading zero(s) recursively from a given phone number if it has a leading zero
 * The base case is when the first number of the string/given cell number is not a 0
 * @param (string) $cellphone The number
 * @return (string) $cellphone without leading zero
 **/
function stripLeadingZero(string $cellphone): string {
	$tempArr2 = [];	
	$tempArr = str_split($cellphone);
	if($tempArr[0] === '0') {
		for($i = 1; $i < count($tempArr); $i++) {
			array_push($tempArr2, $tempArr[$i]);
		}
		$pNumber = implode('', $tempArr2);
		return stripLeadingZero($pNumber);
	} else {
		$number = implode('', $tempArr);
		return $number;
	}
}

/**
 * Generates a simple random string that can be used as a password. A 1 is appended to the string because
 * sometimes it happens that the random string generated does not contain a number!
 * @param $length (int) The length of the string to generate
 * @return $randString (string) A random string of length $length
 **/
function genRandomString(int $length): string {
	if($length > 0) {
		$possibleChars = str_split('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$randString = '';
		for($i = 1; $i <= $length-1; $i++) {
			$randString .=  $possibleChars[rand(0,count($possibleChars))];
		}
		return $randString.'1';
	}
	return false;
}


/**
 * Figure out if a given string is a phone number or en email address
 * @param $string (string) The string assumed email address to validate
 **/
function isValidEmail(string $email): bool {
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	return filter_var($email,FILTER_VALIDATE_EMAIL);
}


/**
 * Load a given helper class class
 **/
function loadHelperClass(string $className): void {
	require_once NEURON_ROOT."/endpoints/helpers/{$className}.php";
}


/**
 * Read an image and return the data stream of it. This is suitable for images to be used by the TCPDF
 * library
 * @param $pathToFile (string) The pathToFile of the image to read its stream. 
 * It is important to note that the $pathToFile should be a complete path to the file in such a way that
 * when the IMG_ASSETS_PATH is appended to it it forms a valid absolute path of the file on the local
 * host. Local host means the file path starts with file protocal and not http protocal. The implication
 * is that we can only use local images in such contexts.
 * @return (string) The read image data stream.
 **/
function getImageDataStream(string $pathToFile): string {
	return '@'.file_get_contents(IMG_ASSETS_PATH.$pathToFile);
}

/**
 * Interpolate image data stream with the $target. For example, it will replace occurrences of [target]
 * with $imageStream in a given string. It is designed to work with the src attribute of the html imagw tag.
 * @param $target (string) The target to search of the form [target]
 * @param $imageStream (string) The image data stream.
 * @param $subject (string) The subject string to perform the interpolation.
 * @return $subject (string) The interpolated string.
 **/
function interpolateImageSource(string $target, string $imageStream, string $subject): string {
	return str_replace($target, $imageStream, $subject);
}

/**
 * Encrypt a given password
 * @param $passwordString (string) The password text string
 **/
function encryptPassword(string $passwordString): string {
	$ciphering = 'AES-128-CTR';
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_key = getenv('ENCRYPTION_KEY');
	return openssl_encrypt($passwordString, $ciphering,$encryption_key, $options, getenv('IV'));
}

/**
 * Decrypt an encrypted password
 * @param $pwd (string) The encrypted password
 * @return (string) The decrypted password
 **/
function decryptPassword(string $pwd): string {
	$ciphering = 'AES-128-CTR';
	$options = 0;
	$iv_length = openssl_cipher_iv_length($ciphering);
	return openssl_decrypt ($pwd, $ciphering,getenv('ENCRYPTION_KEY'), $options, getenv('IV'));
}


/**
 * Load a class given a class name.
 * @param $className (string) The class name of the class to load.
 * @param $parentDir (string) The parent directory of the class to be loaded/required.
 * @return void
 **/
function loadClass(string $className,string $parentDir): void {
	require_once NEURON_ROOT.$parentDir.'/'.$className.'.php';
}

/**
 * Formats a number for display as [+][countryCallingcode] [1d|2d] [3d] [4d]
 **/
function formatCellPhoneNumberForDisplay(string $countryDialCode, string $cellPhoneNumber) {
	//00 000 0000
	$cellPhoneNumber = str_replace(' ','',$cellPhoneNumber);
	$cellPhoneNumber = str_replace('-','',$cellPhoneNumber);
	$cellPhoneNumber = str_replace($countryDialCode,'',$cellPhoneNumber);
	$arr = str_split(stripLeadingZero($cellPhoneNumber));
	$len = count($arr);
	$charCount = 0;
	$addSpaceCount = 0;
	$number = '';
	$partition = 3;
	for ($i = count($arr)-1; $i >= 0 ; $i--) {
		if($charCount > $partition) {
			if($addSpaceCount < 2) {
				$number .= ' ';
				$addSpaceCount++;
				$partition = 2;
				$charCount = 0;
			}
		}
		$number .= $arr[$i];
		$charCount++;
	}
	return $countryDialCode.' '.reverseString($number);
}

/**
 * Reverse a string
 **/
function reverseString($string) {
	$arr = str_split($string);
	$reversed  = '';
	for ($i = count($arr)-1; $i >= 0 ; $i--) {
		$reversed .= $arr[$i];
	}
	return $reversed;
}

/**
 * Remove the last character from the given string
 * @param $string (string) The string to remove the last char
 **/
function stripLastChar(string $string): string {
	$tempArr = str_split($string);
	$tempStr = '';
	for($i=0;$i < count($tempArr)-1; $i++) {
		$tempStr .= $tempArr[$i];
	}
	return $tempStr;
}


/**
 * Get human readable fine formats from a given filezize given in bytes
 * @param $bytes (int) The number of bytes of the file
 * @param $decimals (int) The number of decimals to format the number. The default is 2.
 **/
function humanFilesize(int $bytes, int $decimals = 2): string {
  $sz = 'BKMGTP';
  $suffix = ['B' => ''];
  $factor = floor((strlen($bytes)-1)/3);
  $size = @$sz[$factor];
  $_suffix = $suffix[$size]??'B';
  return sprintf("%.{$decimals}f", $bytes/pow(1024,$factor)).$size.$_suffix;
}

?>
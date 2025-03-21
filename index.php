<?php
/**
** Neuron, a lite backend system
** @author Dawood Feyard Morris Kaundama
** Jun 2021 09:31:01 SAST
** @version 1.0
**/

ini_set('date.timezone','Africa/Harare');

/**
 * Set to dsiplay errors
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
ini_set('log_errors', TRUE);
ini_set('error_log', dirname(__FILE__).'/error_log.log');

session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Cache-Control: no-store');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json; charset=utf-8');

define('ENV_FILE', $_SERVER['DOCUMENT_ROOT'].'/.env.php');


//Load Neuron and resources
if(file_exists(ENV_FILE)) {
	require_once ENV_FILE;
} else {
	require '.env.php';
}
require 'src/Neuron/lib/functions.php';
//require 'src/Neuron/endpoints/helpers/DeveloperErrorReporting.php';
require 'src/Neuron/endpoints/helpers/InputValidator.php';
require 'src/Neuron/endpoints/helpers/Logger.php';
//require 'src/Neuron/endpoints/helpers/ServerConfigs.php';
require 'src/Neuron/Neuron.php';

$data = json_decode(@file_get_contents('php://input'));

file_put_contents(dirname(__FILE__).'/logs/request_logs.log',"[".date('Y_m_d_h_i_s_a')."]: ".json_encode($data ?? ['no_data'=>'no_data'])."\n",FILE_APPEND);

if(!$data) {
	print json_encode(messageInputError());
	exit;
}

if(!authenticateAPISKey(key: $_SERVER['HTTP_APIKEY']??'NOT_SUPPLIED')) {
	print json_encode(messageAuthError());
	exit;
}

/**
 * Some API clients such as axios send the request payload in 'their own data' object. Thus we need to 
 * check if the data object is set on the request payload and grab it as necessary. 
 **/
$Neuron = new Neuron(data: $data->data ?? $data);

$results = $Neuron->process();

print json_encode($results);

?>
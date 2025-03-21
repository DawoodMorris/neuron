<?php
require 'responses/messages.php';
require 'common.dial.codes.php';
require_once 'extras/styled.content.php';

//Styles components
define('CYAN_DIV_START', $cyanDivStart);
define('CYAN_DIV_END', $cyanDivEnd);


define('MAX_PWD_LENGTH', 16);
define('MIN_PWD_LENGTH', 8);

//New line characters
define('NL', "\n");
define('DNL', "\n\n");

/**
 * API  messages/responses constant
 **/
define('MESSAGES', $messages);

//The assets public URL
define('ASSETS_URL', getenv('ASSETS_URL'));

/**
 * Neuron root directory
 */

define('NEURON_ROOT', $_SERVER['DOCUMENT_ROOT'].'/src/Neuron/');

/**
 * Log file for debegging
 **/
define('LOG_FILE',NEURON_ROOT.'logs/logs.lg');

/**
 * Whether to show debug info
 **/
define('DEBUG',false);

/**
 * Mamimum login session period: 30 minutes
 **/
define('MAX_LOGIN_SESS_TIME', 30);

/**
** Database access are defined here
**/
define('_DATABASES', [
    'host' => getenv('DB_HOST'),
    'users' => [
        'test_db' => getenv('DB_USER')
    ],
    'pwds' => [
        'test_db' => getenv('DB_PWD')
    ],
    'names' => [
        'test_db' => getenv('DB_NAME')
    ]
]);


/**
 * Used by the mail API
 **/
define('LN_FD', "\r\n");
define('NW_LN',"\n");

/**
 * From name
 **/
define('NS_WUA_EMAIL_FROM_NAME','Neoron Internals');

?>
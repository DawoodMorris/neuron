<?php
/**
 * Neuron - a framework for building backbone systems. It handles all requests to the backend, 
 * and all user-facing services. More in the readme file where Neuron resides.
 * Written by Dawood Morris Kaundama
 * Jan 2021
 */
class Neuron {
    /**
     * data from the outside which NEURON endpoints use to do their job
     * It is an stcClass object passed to in the contructor
     */
    private object $data;

    //Results of every operation/request
    private array $results = [];

    //does a specified endpoint exist?
    private bool $endpointExists;

    /**
     * Constructor
     */
    function __construct(object $data) {
        file_put_contents(dirname(__FILE__).'/logs/NEURON_logs.log',"[".date('Y-m-d h:i:sa')."]: [".get_class($this)."] ".json_encode($data)."\n",FILE_APPEND);
        $this->data = $data;
        $this->results['data'] = [];
        $this->endpointExists = true;
        $this->loadAuthClasses();
        $this->loadClass();
    }

    /**
     * Log to the system whatever happens
     **/
    private function _syslogLog(): void {
        loadClass(className: 'SystemLogs', parentDir: 'endpoints/helpers');
        $_data = new stdClass;
        $_data->action = $this->data->payload->action;
        $_data->title = 'NEURONRequest';
        $_data->data = $this->data->payload;
        (new SystemLogs(data: $_data))->add();
    }

    /**
     * Process the action on the given endpoint
     */
    public function process(): array {
        $InputValidator = new InputValidator(data: $this->data);
        $inputValidity = $InputValidator->validate(action: get_class($this).'Validator.process');
        if(!($inputValidity->valid)) {
            $this->results['error'] = $inputValidity->error;
            $this->results['message'] = MESSAGES[$inputValidity->error];
            return $this->results;
        }
        try {
            $Endpoint = $this->data->endpoint;
            if(!$this->endpointExists) {
                $Endpoint = 'DefaultEndpoint';
            }
            $notGiven = new stdClass;
            $notGiven->payload = 'notGiven';
            $Endpoint = new $Endpoint(payload: $this->data->payload ?? $notGiven);
            $this->_syslogLog();
            $this->results['data'] = $Endpoint->process();
        } catch (Exception $e) {
            $this->results['error'] = 'serverError';
            $this->results['message'] = MESSAGES['serverError'];
            if(isOnDevServer()) {
                $this->results['debugInfo'] = [
                    'message' => $e->getMessage(),
                    'dump' => $e->__toString()
                ];
            }
        }
        return $this->results;
    }

    /**
     * Load the authentictor class
     **/
    private function loadAuthClasses(): void {
        //load authentication classes here
        require_once 'endpoints/helpers/Authenticator.php';
    }

    /**
     * Attempt to load an endpoint class
     **/
    private function loadClass(): void {
        $endpoint = $this->data->endpoint ?? 'NotGiven';
        $fileName = NEURON_ROOT."endpoints/{$endpoint}.php";
        if(file_exists($fileName)) {
            require_once $fileName;
        } else {
            /**
             * Endpoint does not exist, load default endpoint
             */
            $this->endpointExists = false;
            require_once 'endpoints/DefaultEndpoint.php';
        }
    }
}

?>
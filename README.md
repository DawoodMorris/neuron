This is **Neuron** - A lightweight PHP framework for building robust backend systems.

**Overview**
**Neuron** is a small backend backbone system that can glue multiple API endpoints under one roof. It can be implementted in different dynamic/interpreted languages.

It expects a message request object with props `endpoint`, and `paylod` describing what to do, and the data the 'doing' needs.

The `payload` prop object contains the following mandatory props:
`action`: The action to perfom,
`data`: Data the action needs when necessary to perform the requested action.

Each `action` in every endpoint knows what data it needs to complete its `action`/`request` and the client who requests the service knows what data to supply.

__Example concept:__

```
POST **NEURON**_SERVER_URL/service/serve
headers: {
    apiskey: $api_secret_key_here
}
body {
    endpoint: $endpoint, //e.g. Users,
    payload: {
        action: $action, //e.g. authenticate
        data: {
            //action/request data here, such as:
            username: marscol1,
            password: 123456password
        }
    }
}
```

**Neuron** figures out what endpoint it needs to load to complete the request, calls the `action` action on the `endpoint` and returns the results: A walk through of a given serve goes as follows:
**Neuron**
1. Loads/imports the module responsible to handle the `endpoint` request,
2. Calls the handler as given by the `action` prop with `data` in the `payload` object as the parameter for the `action`.

Example in action (pseudo code):
# Determine handler and call the relevant action on it

try
    const Endpoint = require(request.body.endpoint);
    return Endpoint[request.body.payload.action](request.body.payload.data);
catch (error)
    return error;

**Neuron** returns an object returned by the endpoint. The results object contains a `data` object that has always the following props:
`status`: The status of the action/request. True if successfull, and false otherwise.
`error`: An error signifying what type of error occured during the action fulfilment. This prop is usually set when the `status` prop is false. It is not defined when there is no error.
`message`: A message with feedback about the action/request outcome. This message is only useful when there was an error with the action/request. Usually, when an action was successful, the `message` might be empty.

**Security**
At the header level, **Neuron** authenticates each client request by inspecting the apiskey (given in the request header) and proceeds accordingly depending on the outcome of the authentication.

It is recommended that there be a designated main endpoint through which requests pass through and implement endpoint level authentication.

Note: the test suit is work in progress. It is safe as long as each endpoint has validators.

**Running Neuron**
Make sure you have `clone`d this repo in your local server.

First, spin up MySQL database server or connect to an existing one by supplying credentials in the `.config` directory. There is an sql file (`CreateTestDBUser.sql`) to create a user in the `db` directory to help you get setup and running if you spin up a local MySQL server. Also make sure to create `SystemLogs` database table once your database server is up and running using the `db/SystemLogs.sql` file.

Then `cd` into the directory where you cloned this repo into and run `composer install` to install dependencies. You should be able to run `Neuron` before running `composer install` because you wont need the depencies yet. Then you can install them once you need them. If you do not have `composer` installed, checkout this link to get started with `composer`: https://getcomposer.org/

Then, run the command `./start_dev` to start the PHP local web development server.

You will find an exported REST API client collections in the `rest_api` to quickly get setup and extend from there. There are two collections: one for Postman and the other for Bruno api clients.

**Let it do its thing**
Once you are setup, then its time to get to work. All that remains to do is to implement your endpoints. Place them in the `src/endpoints` directory if they are directly mentioned in the  `endpoint` property of the HTTP request body. Each endpoint should have a correspding input `validator` in the `endpoints/helpers/validators` directory.

We recommend having one dedicated endpoint to handle many client requests and have that endpoint pass requests to the `helper` endpoints in the `endpoints/helpers` directory.

For example, let us define  `UserRequests` endpoint and let all normal client requests be handled by this endpoint, which inturn passes the requests to internal endpoints:

//`src/endpoints/UsersRequests.php`
```
//more code
/**
 * Fetch quick statistics
 **/
private function fetchQuickStats(): array {
    loadClass(className: 'QuickStats', parentDir: 'endpoints/helpers');
    $this->payload->data->action = $this->payload->action;
    $QuickStats = new QuickStats(data: $this->payload->data);
    return $QuickStats->process();
}
```

In the code above, the `UserRequests` endpoint passes the request to the internal endpoint `QuickStats`.

Then, in `QuickStats` internal endpoint, the action `fetchQuickStats` can be implemented:

//`src/endpoints/helpers/QuickStats.php`
```
//more code
/**
 * Fetch quick statistics
 **/
private function fetchQuickStats(): array {
    $InputValidator = new InputValidator(data: $this->data);
    $inputValidity = $InputValidator->validate(action: get_class($this).'Validator.'.$this->data->action);
    if(!($inputValidity->valid)) {
        $this->results['error'] = $inputValidity->error;
        $this->results['message'] = MESSAGES[$inputValidity->error];
        return $this->results;
    }
    $this->results['status'] = true;
    //logic to fetch stats from the db here
    $this->results['stats'] = [
        'visitors' => 17909,
        'visitFrequency' => 231,
        'weightedMean' => 84.6
    ];//etc
    return $this->results;
}
```

If you have issues, open one and we will respond.

Note that this is work in progress, so expect bugs!

Good luck!
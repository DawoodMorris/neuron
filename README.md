# By Dawood Morris Kaundama
__Fri 11 Mar 2022 20:58:59 SAST__

The backend codebase and all system files. The backend is driven by a framework called Neuron in the `serve/` directory.

**Overview**
**Neuron** is a small backend backbone system that can glue multiple API endpoints under one roof. It can be implementted in th dynamic languages.

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
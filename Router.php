<?php
class Router {
    private $map_routes = array();

    /**
     * Adds a user-friendly URI route to any function of a specific class. You can then
     * have access to a reference of the request wrapper in the function as a parameter of the Request class.
     * @param string $uri URI to add
     * @param string $controller Name of the class that contains the function
     * @param string $function Name of the function to call
     * @param array $methods An array of available HTTP methods for this route. Defaults to "GET" only
     * @param bool $public Whether if it is public or not
     */
    public function addRoute(string $uri, string $controller, string $function, array $methods = ["GET"], bool $public = true) {
        $uri = explode("?", $uri)[0];
        $uri = ("/" . trim($uri, "/"));

        $this->map_routes[$uri] = array(
            "controller" => $controller,
            "function" => $function,
            "methods" => $methods,
            "public" => $public
        );
    }

    /**
     * Executes the router and builds the HTTP Request and Response.
     * @return Response The HTTP Response wrapper.
     * It will return different response bodies in JSON depending on:
     * The requested URI does not match with any route.
     * The class or the function do not exist.
     * HTTP method of the request is not allowed for the route.
     * The HTTP method is "OPTIONS" and is available for the route, the body will be the available methods.
     */
    public function run() : Response {
        $request = new Request();

        if (!array_key_exists($request->getEndpoint(), $this->map_routes)) {
            return JsonResponse::detailsResponse(
                details: "Endpoint not found",
                statusCode: StatusCodes::HTTP_404_NOT_FOUND
            );
        }

        $route = $this->map_routes[$request->getEndpoint()];

        $controller = $route["controller"];
        $function = $route["function"];
        $methods = $route["methods"];

        if (!class_exists($controller)) {
            return JsonResponse::detailsResponse(
                details: "The class '$controller' was not found",
                statusCode: StatusCodes::HTTP_501_NOT_IMPLEMENTED
            );
        } else if (!method_exists($controller, $function)) {
            return JsonResponse::detailsResponse(
                details: "Function '$function' from class '$controller' was not found",
                statusCode: StatusCodes::HTTP_501_NOT_IMPLEMENTED
            );
        }
        
        $reflectionMethod = new ReflectionMethod($controller, $function);

        if ($reflectionMethod->isPrivate() || $reflectionMethod->isAbstract()) {
            return JsonResponse::detailsResponse(
                details: "Function '$function' from class '$controller' cannot be called out",
                statusCode: StatusCodes::HTTP_501_NOT_IMPLEMENTED
            );
        }

        if (!in_array($request->getMethod(), $methods)) {
            return JsonResponse::detailsResponse(
                details: $request->getMethod() . " method not allowed",
                statusCode: StatusCodes::HTTP_405_METHOD_NOT_ALLOWED
            );
        } else if ($request->getMethod() == "OPTIONS") {
            return JsonResponse::detailsResponse(
                details: $methods
            );
        }

        $parameters = array();

        foreach($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->getType() == "Request") {
                $parameters[$parameter->getName()] = $request;

                break;
            }
        }

        if (in_array("request", $reflectionMethod->getParameters())) {
            $parameters["request"] = $request;
        } else if (in_array("req", $reflectionMethod->getParameters())) {
            $parameters["req"] = $request;
        }

        if ($function instanceof \Closure) {
            $function();
        } else {
            $controller = new $controller();
            //$response = $controller->$function(request: $request);
            $response = $reflectionMethod->invokeArgs($controller, $parameters);
        }

        return $response;
    }
}
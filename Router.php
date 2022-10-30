<?php
class Router {
    private $map_routes = array();

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

        if (!method_exists($controller, $function)) {
            return JsonResponse::detailsResponse(
                details: "Function '$function' from class '$controller' was not found",
                statusCode: StatusCodes::HTTP_501_NOT_IMPLEMENTED
            );
        }

        if (!in_array($request->getMethod(), $methods)) {
            return JsonResponse::detailsResponse(
                details: $request->getMethod() ." method not allowed",
                statusCode: StatusCodes::HTTP_405_METHOD_NOT_ALLOWED
            );
        } else if ($request->getMethod() == "OPTIONS") {
            return JsonResponse::detailsResponse(
                details: $methods,
                statusCode: StatusCodes::HTTP_200_OK
            );
        }

        if ($function instanceof \Closure) {
            $function();
        } else {
            $controller = new $controller();
            $response = $controller->$function(request: $request);
        }

        return $response;
    }
}
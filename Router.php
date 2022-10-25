<?php
class Router {
    private $map_routes = array();

    public function addRoute(string $uri, string $controller, string $method, bool $public = true) {
        $uri = ("/" . trim($uri, "/"));

        $this->map_routes[$uri] = array(
            "controller" => $controller,
            "method" => $method,
            "public" => $public
        );
    }

    public function run() : Response {
        $response = new Response();

        $uri_get = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "/");

        if (!array_key_exists($uri_get, $this->map_routes)) {
            return JsonResponse::detailsResponse(
                details: "Endpoint not found",
                statusCode: StatusCodes::HTTP_404_NOT_FOUND
            );
        }

        $route = $this->map_routes[$uri_get];

        $controller = $route["controller"];
        $method = $route["method"];

        if (!method_exists($controller, $method)) {
            return JsonResponse::detailsResponse(
                details: "Method '$method' from class '$controller' was not found",
                statusCode: StatusCodes::HTTP_501_NOT_IMPLEMENTED
            );
        }

        if ($method instanceof \Closure) {
            $method();
        } else {
            $request = new Request();
            $request->setBody("Test");
            $controller = new $controller();
            $controller->$method($request);
        }

        return $response;
    }
}
<?php
class JsonResponse extends Response {
    public function __construct(mixed $body = "", array $headers = array(), array $cookies = array(), int $statusCode = 200) {
        $headers["Content-Type"] = "application/json;charset=UTF-8";

        if (!is_string($body)) {
            $body = JSON::stringify($body);
        }
        
        parent::__construct($body, $headers, $cookies, $statusCode);
    }

    public static function detailsResponse(mixed $details = "Endpoint not found", int $statusCode = 404) : JsonResponse {
        //header("Content-type: application/json");

        return new JsonResponse(
            body: array("details" => $details),
            statusCode: $statusCode
        );
    }
}
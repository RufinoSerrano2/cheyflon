<?php
class JsonResponse extends Response {
    public function __construct(mixed $body = "", array $headers = [], array $cookies = [], int $statusCode = 200) {
        header("Content-type: application/json");

        $body = JSON::stringify($body);
        parent::__construct($body, $headers, $cookies, $statusCode);
    }

    public static function detailsResponse(string $details = "Endpoint not found", int $statusCode = 404) : JsonResponse {
        //header("Content-type: application/json");

        return new JsonResponse(
            body: array("details" => $details),
            statusCode: $statusCode
        );
    }
}
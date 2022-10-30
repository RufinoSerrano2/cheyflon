<?php
require "Response.php";

class Request {
    private string $endpoint;
    private string $method;
    private string $body;
    private array $headers;
    private array $cookies;

    public function __construct() {
        $this->method = strtoupper(htmlentities($_SERVER["REQUEST_METHOD"]));
        $this->endpoint = (isset($_SERVER["REQUEST_URI"]) ? explode("?", htmlentities($_SERVER["REQUEST_URI"]))[0] : "/");
        $this->body = JSON::stringify($this->getRequestBody());
    }

    /**
     * Get the value of endpoint
     */ 
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the value of endpoint
     *
     * @return  self
     */ 
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get the value of body
     */ 
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @return  self
     */ 
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of headers
     */ 
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the value of headers
     *
     * @return  self
     */ 
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get the value of cookies
     */ 
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Set the value of cookies
     *
     * @return  self
     */ 
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * Get the value of method
     */ 
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the value of method
     *
     * @return  self
     */ 
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    private function getRequestBody() {
        return match ($this->getMethod()) {
            "GET" => filter_var_array($_GET),
            "POST" => $this->getPOSTBody(),
            "PUT" => $this->getInputBody("PUT"),
            "DELETE" => $this->getInputBody("DELETE"),
            default => ""
        };
    }

    private function getPOSTBody() {
        if (!empty($_POST)) {
            $body = filter_var_array($_POST);
        } else {
            $body = filter_var(file_get_contents("php://input"), FILTER_SANITIZE_SPECIAL_CHARS);
        }
    
        return $body;
    }
    
    private function getInputBody() {
        parse_str(filter_var(file_get_contents("php://input"), FILTER_SANITIZE_SPECIAL_CHARS), $input);
    
        foreach ($input as $key => $value) {
            unset($_PUT[$key]);
    
            $input[str_replace("amp;", "", $key)] = $value;
        }
    
        return filter_var_array($input);
    }

    /**
     * Parses and returns the request body as an array
     * @return Request body as an array
     */
    public function JSON() : array {
        return JSON::parse($this->getBody(), true);
    }

    /**
     * Creates an HTTP GET request
     * @param string $url API Endpoint
     * @param array|string $data HTTP GET body data / URL query. Defaults to an empty array
     * @param array $headers HTTP Headers array. Defaults to an empty array
     * @return Response HTTP Response wrapper
     */
    public static function get(string $url, array|string $data = [], array $headers = []) : Response {
        return Request::sendRequest($url, "GET", $data, $headers);
    }

    /**
     * Creates an HTTP POST request
     * @param string $url API Endpoint
     * @param array|string $data HTTP POST body data. Defaults to an empty array
     * @param array $headers HTTP Headers array. Defaults to an empty array
     * @return Response HTTP Response wrapper
     */
    public static function post(string $url, array|string $data = [], array $headers = []) : Response {
        return Request::sendRequest($url, "POST", $data, $headers);
    }

    /**
     * Sends an HTTP request
     * @return Response HTTP Response wrapper
     */
    public function send() : Response {
        return Request::sendRequest($this->getEndpoint(), $this->getMethod(), $this->getBody(), $this->getHeaders());
    }

    /**
     * Sends an HTTP request
     * @param string $url API Endpoint
     * @param string $method HTTP method. Defaults to "GET"
     * @param array|string $data HTTP body data / URL query. Defaults to an empty array
     * @param array $headers HTTP Headers array. Defaults to an empty array
     * @return Response HTTP Response wrapper
     */
    public static function sendRequest(string $url, string $method = "GET", array|string $data = [], array $headers = []) : Response {
        $response = new Response();
        
        if (is_array($data)) {
            $data = JSON::stringify($data);
        }

        $ch = curl_init();
        
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "utf-8",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 1000,
            CURLOPT_TIMEOUT        => 1000,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => $headers
        );

        curl_setopt_array($ch, $options);

        $headers = [];
        
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(":", $header, 2);

                if (count($header) < 2) {
                    return $len;
                }

                $headers[strtolower(trim($header[0]))] = trim($header[1]);
                
                return $len;
            }
        );

        $curl_response = curl_exec($ch);
        curl_close($ch);

        if ($curl_response === false)  {
            throw new Exception(curl_error($ch));
        }

        $http_status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($curl_response, $header_size);

        $response->setBody(stripslashes($body));
        $response->setStatusCode($http_status);
        $response->setHeaders($headers);

        return $response;
    }
}
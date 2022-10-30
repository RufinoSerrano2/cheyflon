<?php

require "JsonResponse.php";

class Response {
    private string $body;
    private $headers;
    private array $cookies;
    private int $statusCode;

    public function __construct(string $body = "", array $headers = [], array $cookies = [], int $statusCode = StatusCodes::HTTP_200_OK) {
        $this->body = $body;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->statusCode = $statusCode;
    }

    /**
     * Get the value of body
     */ 
    public function getBody()
    {
        return (isset($this->body) ? $this->body : "");
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

    public function JSON() {
        return JSON::parse($this->getBody(), true);
    }

    /**
     * Get the value of statusCode
     */ 
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set the value of statusCode
     *
     * @return  self
     */ 
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
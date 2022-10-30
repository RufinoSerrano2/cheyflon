<?php

require "./Router.php";
require "./http/Request.php";
require "./http/StatusCodes.php";

require "./Connection.php";
require "./PokemonController.php";
require "./formats/JSON.php";
require "./formats/XML.php";

$router = new Router();
$router->addRoute(uri: "/pokemon", controller: "PokemonController", function: "list", methods: ["GET", "POST", "OPTIONS"]);
$router->addRoute(uri: "/pokemon/test", controller: "PokemonController", function: "list");
$response = $router->run();

http_response_code($response->getStatusCode());

/*if (!in_array("Content-Type", $response->getHeaders())) {
  header("Content-Type: application/json");
}*/

foreach ($response->getHeaders() as $key => $value) {
    header("$key: $value");
}

echo $response->getBody();

/*$res = Request::post("https://reqres.in/api/register", '{
  "email": "eve.holt@reqres.in",
  "password": "pistol"
}', $headers=["Content-Type: application/json"]);*/

//$a = JSON::parseObject("{}", "Person");
//echo $res->getBody();

//echo $a->per_page;
//echo JSON::stringify($a);
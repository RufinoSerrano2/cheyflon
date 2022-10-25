<?php

// Include router class
require "./Router.php";
require "./http/Request.php";
require "./http/StatusCodes.php";

require "./Connection.php";
require "./PokemonController.php";
require "./JSON.php";
require "./XML.php";

$router = new Router();

/*$router->add('/',function(){
  echo "Hola Mundo - Esta es una ruta simple";
});*/

$router->addRoute("/pokemon", "PokemonController", "list");
$router->addRoute("/pokemon/test", "PokemonController", "lista");
$res = $router->run();

http_response_code($res->getStatusCode());
echo $res->getBody();

/*$res = Request::post("https://reqres.in/api/register", '{
  "email": "eve.holt@reqres.in",
  "password": "pistol"
}', $headers=["Content-Type: application/json"]);*/

//$a = JSON::parseObject("{}", "Person");
//echo $res->getBody();

//echo $a->per_page;
//echo JSON::stringify($a);
<?php
require "../bootstrap.php";
use Src\Controller\PersonController;
use Src\Controller\ProdottiController;
use Src\Controller\OrdineController;
use Src\Controller\TipoQuantitaController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /person
// everything else results in a 404 Not Found
if ($uri[1] !== "prodotti" && $uri[1] !== "ordine" && $uri[1] !== "getordinialimento" && $uri[1] !== "tipoquantita") { // || $uri[1] !== "orario"
    header("HTTP/1.1 405 Not Found");
    exit();
}elseif($uri[1] === 'ordine'){
    $method = "ordine";
}elseif($uri[1] === 'prodotti'){
    $method = "prodotti";
}elseif($uri[1] === 'getordinialimento'){
    $method = "getordinialimento";
}elseif($uri[1] === 'tipoquantita'){
    $method = "tipoquantita";
}

// the user id is, of course, optional and must be a number:
$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
switch ($method){
    case "prodotti":
        $controller = new ProdottiController($dbConnection, $requestMethod, $userId);
        $controller->processRequest();
        break;
    case "ordine":
        $controller = new OrdineController($dbConnection, $requestMethod, $userId);
        $controller->processRequest();
        break;
    case "getordinialimento":
        $controller = new OrdineController($dbConnection, $requestMethod, $userId);
        $controller->getalimento($userId);
        break;
    case "tipoquantita":
        $controller = new TipoQuantitaController($dbConnection, $requestMethod, $userId);
        $controller->processRequest($userId);
        break;
    default:
        exit(json_encode("Non conosco la rotta"));
        break;
}
<?php
require '../helpers.php';

// Autoload classes from the Framework directory
spl_autoload_register(function ($class) {
    $path = basePath('Framework/') . $class . '.php';

    if (file_exists($path)) {
        require $path;
    }
});

// Instantiate the router
$router = new Router();
// Get the routes
$routes = require basePath('routes.php');

//  Get current URI and http method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method);

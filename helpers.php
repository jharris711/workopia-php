<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */
function basePath($path = '') {
    return __DIR__ . '/' . $path;
}

/**
 * Load a view
 * 
 * @param string $name
 * @return void
 */
function loadView($name) {
    $viewPath = require basePath("views/{$name}.view.php");

    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        echo "View not found";
    }
}

/**
 * Load a view
 * 
 * @param string $name
 * @return void
 */
function loadPartial($name) {
    $partialPath = basePath("views/partials/{$name}.php");

    if (file_exists($partialPath)) {
        require $partialPath;
    } else {
        echo "Partial not found";
    }
}

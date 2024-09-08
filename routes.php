<?php

// Home
$router->get('/', 'HomeController@index');

// Listings
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create');
$router->get('/listings/edit/{id}', 'ListingController@edit');
$router->get('/listings/{id}', 'ListingController@show');

$router->post('/listings', 'ListingController@store');
$router->put('/listings/{id}', 'ListingController@update');
$router->delete('/listings/{id}', 'ListingController@destroy');


// Users / Authentication
$router->get('/auth/register', 'UserController@create');
$router->get('/auth/login', 'UserController@login');

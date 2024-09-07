<?php

// Home
$router->get('/', 'HomeController@index');

// Listings
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create');
$router->get('/listing/{id}', 'ListingController@show');
$router->post('/listings', 'ListingController@store');

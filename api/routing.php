<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers origin, x-requested-with, content-type");
header("Access-Control-Allow-Methods PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Expose-Headers: Set-Cookie");

error_reporting(E_ALL);
ini_set('display_errors', '1');


require "services/DB.php";
require_once "vendor/autoload.php";
use api\Api;

require('controllers/UsersController.php');
require('controllers/AuthController.php');
require('Api.php');

// Getting current URL
$current_link = $_SERVER['REQUEST_URI'];
// Handling query string.
if(str_contains($current_link, '?')) {
    $current_link = explode('?', $current_link)[0];
}
$param = explode('/', $current_link);

// Routes
$urls = [
  '/api/user/create' => ['UsersController@createRandomUser'],
  '/api/user/createMany' => ['UsersController@createMany'],
  '/api/profiles' => ['UsersController@possibleMatches'],
  '/api/profilesImproved' => ['UsersController@possibleMatchesImproved'],
  '/api/swipe' => ['UsersController@swipe'],
  '/api/login' => ['AuthController@login'],
  '/api/logout' => ['AuthController@logout'],
];

// Check if route available.
$availableRoutes = array_keys($urls);

//Throw 404 if not part of our urls
if(!in_array($current_link, $availableRoutes)) {
    header('HTTP/1.0 404 Not found');
    exit;
}

//Call our routing handle function
Api::routing($current_link, $urls);

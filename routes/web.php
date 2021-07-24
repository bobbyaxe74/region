<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return view('welcome');
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

$router->get('/author', function () use ($router) {
    return response()->json('See https://github.com/bobbyaxe61',200);
});

$router->group(['middleware' => ['throttle:1,1'], 'prefix' => 'api/earth'],function () use ($router) {

    $router->get('index', 'EarthRegionController@index');
    $router->post('sort/index', 'EarthRegionController@sortIndex');
});

$router->group(['middleware' => ['throttle:1,1'], 'prefix' => 'api/mars'],function () use ($router) {

    $router->get('index', 'MarsRegionController@index');
    $router->post('sort/index', 'MarsRegionController@sortIndex');
});
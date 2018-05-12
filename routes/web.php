<?php

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

$router->get('/', 'ExampleController@showVersion');
$router->get('/public_holidays', 'ReservationController@publicHoliday');

$router->group(['prefix' => 'rooms'], function () use ($router) {
  $router->get('/', 'RoomController@fetchAll');
  $router->get('/{room_id}', 'RoomController@fetchOne');
});


$router->group(['prefix' => 'reservations'], function () use ($router) {
  $router->get('/{reservation_id}', 'ReservationController@fetchOne');
  $router->get('/', 'ReservationController@fetchAll');
  $router->post('/', 'ReservationController@create');
  
});

<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Authentication routes...
Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
  Route::get('login', 'AuthController@getLogin');
  Route::post('login', 'AuthController@postLogin');
  Route::get('logout', 'AuthController@getLogout');

  Route::get('register', 'AuthController@getRegister');
  Route::post('register', 'AuthController@postRegister');
});

// $app->group(['prefix' => 'api/v1/{service}','namespace' => 'App\Http\Controllers'], function($app)
// {
//   $app->get('saveToken/{user_id}', 'ServiceController@saveToken');
// });

Route::controller('/profiles', 'ProfilesController');
Route::controller('/', 'HomeController');

// Route::get('/', ['middleware' => 'auth', function() {
//     return view('dashboard');
// }]);

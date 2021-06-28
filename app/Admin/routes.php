<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    $router->get('books','BookController@index');
    $router->get('books/create','BookController@create');
    $router->post('books','BookController@store');
    $router->get('books/{id}/edit','BookController@edit');
    $router->put('books/{id}','BookController@update');
    
    $router->get('users','UserController@index');

    $router->get('records','RecordController@index')->name('records.index');
    $router->get('records/create','RecordController@create');
    $router->post('records','RecordController@store');
    $router->get('records/{record}','RecordController@show');
    $router->get('records/{id}/edit','RecordController@edit');
    $router->post('records/back','RecordController@bookReturn')->name('records.return');        
});

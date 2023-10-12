<?php

$routes->group('api/login', ['namespace' => 'App\Modules\Api\Controllers'], 
  function($routes) {
    $routes->get('/', 'Login::index_get');
    $routes->post('/', 'Login::index_post');
    $routes->delete('/', 'Login::index_delete');
});

?>
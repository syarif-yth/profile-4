<?php

use Example\Controllers\ExampleController;

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('examples', [ExampleController::class, 'index'], ['as' => 'example.index']);
    $routes->get('example', fn () => redirect()->to(route_to('example.index')));
    $routes->get('example/(:any)', [ExampleController::class, 'show'], ['as' => 'example.show']);
    $routes->post('example', [ExampleController::class, 'store'], ['as' => 'example.store']);
    // $routes->get('example/(:num)/edit', [ExampleController::class, 'edit'], ['as' => 'example.edit']);
    $routes->post('example/(:num)/update', [ExampleController::class, 'update'], ['as' => 'example.update']);
    $routes->delete('example/(:num)', [ExampleController::class, 'delete'], ['as' => 'example.delete']);

    // Example sub-routes
    $routes->group('example', static function ($routes) {
        // ...
    });
});

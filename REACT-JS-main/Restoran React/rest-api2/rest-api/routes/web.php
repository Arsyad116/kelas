<?php

/** @var Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
$router->group(['prefix' => 'api'], function () use ($router) {
    // Customer auth routes
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('register', ['uses' => 'AuthController@register', 'as' => 'auth.register']);
        $router->post('login', ['uses' => 'AuthController@login', 'as' => 'auth.login']);
    });

    // Staff auth routes
    $router->group(['prefix' => 'staff/auth'], function () use ($router) {
        $router->post('register', ['uses' => 'StaffAuthController@register', 'as' => 'staff.auth.register']);
        $router->post('login', ['uses' => 'StaffAuthController@login', 'as' => 'staff.auth.login']);
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {
    // Customer auth routes
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('logout', ['uses' => 'AuthController@logout', 'as' => 'auth.logout']);
        $router->get('me', ['uses' => 'AuthController@me', 'as' => 'auth.me']);
    });

    // Staff auth routes
    $router->group(['prefix' => 'staff/auth'], function () use ($router) {
        $router->post('logout', ['uses' => 'StaffAuthController@logout', 'as' => 'staff.auth.logout']);
        $router->get('me', ['uses' => 'StaffAuthController@me', 'as' => 'staff.auth.me']);
    });

    // Admin-only routes
    $router->group(['prefix' => 'admin', 'middleware' => 'admin'], function () use ($router) {
        // Customer management
        $router->group(['prefix' => 'pelanggan'], function () use ($router) {
            $router->get('/', ['uses' => 'PelangganController@index', 'as' => 'admin.customers.index']);
            $router->post('/', ['uses' => 'PelangganController@store', 'as' => 'admin.customers.store']);
            $router->get('/{id}', ['uses' => 'PelangganController@show', 'as' => 'admin.customers.show']);
            $router->put('/{id}', ['uses' => 'PelangganController@update', 'as' => 'admin.customers.update']);
            $router->delete('/{id}', ['uses' => 'PelangganController@destroy', 'as' => 'admin.customers.destroy']);
            $router->post('/{id}/restore', ['uses' => 'PelangganController@restore', 'as' => 'admin.customers.restore']);
        });

        // Staff management
        $router->group(['prefix' => 'staff'], function () use ($router) {
            $router->get('/', ['uses' => 'StaffController@index', 'as' => 'admin.staff.index']);
            $router->post('/', ['uses' => 'StaffController@store', 'as' => 'admin.staff.store']);
            $router->get('/{id}', ['uses' => 'StaffController@show', 'as' => 'admin.staff.show']);
            $router->put('/{id}', ['uses' => 'StaffController@update', 'as' => 'admin.staff.update']);
            $router->delete('/{id}', ['uses' => 'StaffController@destroy', 'as' => 'admin.staff.destroy']);
            $router->post('/{id}/restore', ['uses' => 'StaffController@restore', 'as' => 'admin.staff.restore']);
        });
    });

    // Manager-only routes
    $router->group(['prefix' => 'manager', 'middleware' => 'manager'], function () use ($router) {
        // Reports routes
        $router->get('/reports', ['uses' => 'ReportController@index', 'as' => 'manager.reports.index']);
    });

    // kasir-only routes
    $router->group(['prefix' => 'kasir', 'middleware' => 'kasir'], function () use ($router) {
        // Order routes
        $router->post('/orders', ['uses' => 'OrderController@store', 'as' => 'kasir.orders.store']);
    });

    // Shared routes for all authenticated users
    $router->group(['prefix' => 'menu'], function () use ($router) {
        $router->get('/', ['uses' => 'MenuController@index', 'as' => 'menu.index']);
        $router->get('/{id}', ['uses' => 'MenuController@show', 'as' => 'menu.show']);
    });
});

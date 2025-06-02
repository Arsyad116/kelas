<?php

namespace App\Providers;

use App\Models\Pelanggan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('api', function ($request) {
            return $request->user();
        });

        Auth::viaRequest('pelanggan', function ($request) {
            if ($request->input('api_token')) {
                return Pelanggan::where('api_token', $request->input('api_token'))->first();
            }
        });


        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return Pelanggan::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}

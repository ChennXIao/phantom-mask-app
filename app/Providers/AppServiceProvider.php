<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data, $message = null, $apiCode = '0000', $httpStatusCode = 200) {
            $message = $message ?? config('api_code.' . $apiCode);
            return Response::json([
                'metadata' => [
                    'status' => $apiCode,
                    'message' => $message,
                ],
                'data' => $data,
            ], $httpStatusCode);
        });

        Response::macro('error', function ($message = null, $apiCode = '5000', $httpStatusCode = 400, $data = null) {
            $message = $message ?? config('api_code.' . $apiCode);
            return Response::json([
                'metadata' => [
                    'status' => $apiCode,
                    'message' => $message,
                ],
                'data' => $data,
            ], $httpStatusCode);
        });
    }
}

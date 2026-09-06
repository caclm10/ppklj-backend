<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('api', function (bool $success, int $status = 200, ?string $message = null, mixed $data = null) {
            $payload = [
                'success' => $success,
                'status' => $status,
            ];

            if ($message !== null) {
                $payload['message'] = $message;
            }

            if ($data !== null) {
                $payload['data'] = $data;
            }

            return response()->json($payload, $status);
        });
    }
}

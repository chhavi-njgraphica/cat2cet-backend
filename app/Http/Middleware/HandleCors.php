<?php
namespace App\Http\Middleware;

use Illuminate\Http\Middleware\HandleCors as Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleCors extends Middleware
{
    protected function setCorsHeaders($request, $response)
    {
        return tap($response, function (Response $response) use ($request) {
            $response->headers->set('Access-Control-Allow-Origin', env('LARAVEL_CORS_ORIGIN', '*'));
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        });
    }
}

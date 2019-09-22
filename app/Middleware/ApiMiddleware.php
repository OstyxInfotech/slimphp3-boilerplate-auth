<?php

namespace App\Middleware;

use App\Auth\Jwt;

class ApiMiddleware extends Middleware
{
    public function __invoke ($request, $response, $next)
    {
        $token = $request->getHeader('Authorization');
        if (!$token) return $response->withJson(['message' => 'Not Authorized'], 401);
        if (!Jwt::verify($token[0])) {
            return $response->withJson(['message' => 'Not Authorized'], 401);
        }

        $response = $next($request, $response);
        return $response;
    }
}
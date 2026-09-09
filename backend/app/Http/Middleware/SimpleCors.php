<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SimpleCors
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin');

        if ($origin) {
            $response = $request->getMethod() === 'OPTIONS'
                ? response('', 204)
                : $next($request);

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN, Accept, Origin');
            $response->headers->set('Access-Control-Expose-Headers', 'X-XSRF-TOKEN');

            if ($request->getMethod() === 'OPTIONS') {
                $response->setStatusCode(204);
            }

            return $response;
        }

        return $next($request);
    }
}

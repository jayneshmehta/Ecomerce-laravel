<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authentication = $request->header('Authorization');
        $accessToken = User::where("token","=",$authentication)->first();
        if(!$accessToken){
            return response()->json([
                "status" => false, 
                'message'=> "You are Unauthorized",
            ],401);
        }
        return $next($request);
    }
}

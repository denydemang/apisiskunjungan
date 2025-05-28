<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        $authenticate =true; //status authentikasi

        if (!$token)  //jika tidak ada token
        {
            $authenticate =false; //ubah authenticate menjadi false
        } else {
            $user = User::where("remember_token" , $token )->first();
            $authenticate = !!$user;
        }

        if ($authenticate) //jika user memang sudah login/terauthorized
        {
            return $next($request);
        } else { //jika tidak terauthorized
            return response()->json([
                "errors" =>[
                    "general" => [
                        "You Are Unauthorized"
                    ]
                ]
        ])->setStatusCode(401);
        }
    
    }

}

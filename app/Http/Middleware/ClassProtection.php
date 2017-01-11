<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $idClass = $request->route()->parameter("id");
        $user = Auth::guard('api')->user();
        // This all can be simplified (applies to all Protection middlewares):
//        $userRole = DB::table('classes_user')
//            ->select('role')
//            ->where('user_id', $user->id)
//            ->where('classes_id', $idClass)
//            ->whereIn('role', ['admin', 'owner'])
//            ->get();
//        if ($userRole) {
//            return $next($request);
//        }
        $userRole = DB::table('classes_user')->select('role')->where('user_id', '=', $user->id)->where('classes_id', '=', $idClass)->get();
        if(count($userRole)>0) {
            // Does this even work? We call toArray on array
            if ($userRole->toArray()[0]->role == "admin" || $userRole->toArray()[0]->role == "owner") {
                return $next($request);
            }
        }

        // It may be a good idea to throw an exception here and handle these types of exceptions uniformly in exception handler
        return response()->json(['error' => 'no access rights'], 403);
    }
}

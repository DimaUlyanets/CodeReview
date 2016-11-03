<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupProtection
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
        $idGroup = $request->route()->parameter("id");
        $user = Auth::guard('api')->user();
        $userRole = DB::table('group_user')->select('role')->where('user_id', '=', $user->id)->where('group_id', '=', $idGroup)->get();
        if(count($userRole)>0) {
            if ($userRole->toArray()[0]->role == "admin" || $userRole->toArray()[0]->role == "owner") {
                return $next($request);
            }
        }
        return response()->json(['error' => 'no access rights'], 403);
    }
}

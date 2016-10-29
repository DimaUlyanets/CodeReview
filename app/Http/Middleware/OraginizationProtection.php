<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OraginizationProtection
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
        $idOrganization = $request->route()->parameter("id");
        $user = Auth::guard('api')->user();
        $userRole = DB::table('organization_user')->select('role')->where('user_id', '=', $user->id)->where('organization_id', '=', $idOrganization)->get();
        if(count($userRole)>0) {
            if ($userRole->toArray()[0]->role == "admin" || $userRole->toArray()[0]->role == "owner") {
                return $next($request);
            }
        }
        return response()->json(['error' => 'no access rights'], 403);
    }
}

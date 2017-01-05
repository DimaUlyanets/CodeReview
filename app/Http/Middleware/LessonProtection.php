<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonProtection
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
        $id = $request->route()->parameter("id");
        $user = Auth::guard('api')->user();
        $userAuthor = DB::table('lessons')->select()->where('author_id', '=', $user->id)->where('id', '=', $id)->get();
        if(count($userAuthor)>0) {
           return $next($request);
        }
        return response()->json(['error' => 'no access rights'], 403);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class showTask
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id=$request->route('id');
        $user_id = auth('api')->user()->id;
        if ($user_id && $user_id==$id) {
            $group = Group::where('user_id', $user_id)->get();
            if ($group->count()>0) {
                return $next($request);
            }
            return response()->json([
                "message" => "You are not part of any working group."
            ], 404);
        }
        return \response()->json(['message'=>'No authentication'],404);
    }
}

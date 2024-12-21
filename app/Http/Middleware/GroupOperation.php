<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupOperation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $group=Group::where('owner_id',auth('api')->user()->id)->first();
       if($group){
           return $next($request);
       }
       return \response()->json([
           "message"=>"You don,t Have this Group "
       ],404);
    }
}

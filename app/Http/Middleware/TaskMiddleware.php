<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project_id = $request->route('id');
        $project = Project::where('id', $project_id)->first();
        if ($project && auth('api')->check() && auth('api')->user() &&$project->user_id==auth('api')->user()->id) {
            return $next($request);
        }
        if(!$project){
            return response(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
}

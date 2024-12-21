<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompletedTask
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $task_id=$request->route('id');
        $task=Task::where('id',$task_id)->first();
        if($task->assigned_to != auth('api')->user()->id){
            return \response()->json([
                'message'=>"You do not have the authority to change or delete this task. ",
            ],404);

        }

        return $next($request);
    }
}

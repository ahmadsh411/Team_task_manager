<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Returntask
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $group=Group::where('user_id',auth('api')->user()->id)->first();
        if($group){
            $task=Task::where('id',$group->task_id)->first();
            if($task->assigned_to!=null){
                return \response()->json([
                   "message"=> "Task Is Completed"
                ],404);
            }

            return $next($request);
        }
    }
}

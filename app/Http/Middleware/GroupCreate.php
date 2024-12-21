<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

// استيراد الكلاس الصحيح
use Closure;
use Illuminate\Http\Request;

class GroupCreate
{

    public function handle(Request $request, Closure $next)
    {
        try {
            // التحقق من المستخدم
            $user = User::where('name', $request->name)
                ->where('id', $request->id)
                ->firstOrFail();

            // التحقق من المشروع
            $project = Project::findOrFail($request->route('id'));

            // التحقق من المهمة
            $task = Task::findOrFail($request->route('task_id'));

            // التحقق إذا كانت المجموعة موجودة مسبقًا
            $group = Group::where('user_id', $user->id)
                ->where('project_id', $project->id)
                ->where('task_id', $task->id)->where('owner', auth('api')->user()->name)
                ->where('owner_id', auth('api')->user()->id)
                ->first();

            if ($group || ($request->id == auth('api')->user()->id && $request->name == auth('api')->user()->name)) {
                // إذا كانت المجموعة موجودة
                if ($request->route('task_id') == $group->task_id) {
                    return response()->json([
                        "message" => "The task  " . $group->task_name . " already exists on the project " . $group->project_name
                    ], 500);
                }

                return response()->json([
                    'message' => 'Group Exists!'
                ], 409); // Conflict
            }

            // إذا لم توجد مجموعة، السماح بالمتابعة
            return $next($request);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // إذا لم يتم العثور على المستخدم، المشروع، أو المهمة
            return response()->json([
                'message' => 'Resource not found: ' . $e->getMessage()
            ], 404); // Not Found

        } catch (\Exception $e) {
            // أخطاء أخرى
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
}

<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\AdminInterface;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Traits\MessagesStatus;
use http\Client\Curl\User;
use http\Env\Response;

class AdminRepository implements AdminInterface
{
    use MessagesStatus;

    public function index()
    {
        $data = [];
        $projects = Project::with(['manager', 'groups', 'tasks'])->get(); // استخدام eager loading لتحسين الأداء

        foreach ($projects as $project) {
            $data[] = [
                "manager_project" => $project->manager->name ?? 'N/A', // تحقق من وجود القيم لتجنب الأخطاء
                "manager_email" => $project->manager->email ?? 'N/A',
                "Group" => $project->groups->map(function ($group) { // التعامل مع المجموعات باستخدام map
                    return [
                        "user_name" => $group->user_name ?? 'N/A',
                        "user_id" => $group->user_id ?? 'N/A',
                    ];
                }),
                "Task" => $project->tasks->map(function ($task) { // التعامل مع المهام باستخدام map
                    return [
                        "name" => $task->name ?? 'N/A',
                        "title" => $task->title ?? 'N/A',
                        "description" => $task->description ?? 'N/A',
                        "status" => $task->status ?? 'N/A',
                        "complete_time" => $task->complete_time ?? 'N/A',
                        "complete_description" => $task->complete_description ?? 'N/A',
                        "assigned_to" => $task->assigned_to ?? 'N/A',
                    ];
                }),
                "message" => $this->sendMessageStatus(200),
            ];
        }

        return response()->json([
            'data' => $data,
        ], 200);
    }


    public function store($request)
    {
        // TODO: Implement store() method.
    }

    public function update($id, $request)
    {
        // TODO: Implement update() method.
    }

    public function destroyTask($id)
    {
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return response()->json(
                [
                    "message" => $this->sendMessageStatus(404),
                ], 404
            );
        }
        $task->delete();
        return response()->json([
            "message" => $this->sendMessageStatus(200)], 200);
    }

    public function destroyProject($id)
    {
        $project = Project::where('id', $id)->first();
        if (!$project) {
            return response()->json([
                "message" => $this->sendMessageStatus(404),
            ], 404);
        }
        $project->delete();

        return response()->json([
            "message" => $this->sendMessageStatus(200)], 200);
    }

    public function destroyGroup($id)
    {
        $group = Group::where('id', $id)->first();
        if (!$group) {
            return response()->json([
                "message" => $this->sendMessageStatus(404),
            ], 404);
        }
        $group->delete();

        return response()->json([
            "message" => $this->sendMessageStatus(200)], 200);

    }


    public function showTasks()
    {
        $data = [];
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $data[] = [
                "name" => $task->name ?? 'N/A',
                "title" => $task->title ?? 'N/A',
                "description" => $task->description ?? 'N/A',
                "status" => $task->status ?? 'N/A',
                "complete_time" => $task->complete_time ?? 'N/A',
                "complete_description" => $task->complete_description ?? 'N/A',
                "assigned_to" => $task->assigned_to ?? 'N/A',
            ];
        }

        return response()->json([
            "message" => $this->sendMessageStatus(200),
            "data" => $data,
        ], 200);

    }

    public function showGroups()
    {
        $groups = Group::all();
        $data = [];
        foreach ($groups as $group) {
            $data[] = [
                "user_name" => $group->user_name ?? 'N/A',
                "user_id" => $group->user_id ?? 'N/A',
                "owner" => $group->owner,
                "owner_id" => $group->owner_id,
                "project_id" => $group->project_id,
                "project_name" => $group->project_name,
                "task_id" => $group->task_id,
                "task_name" => $group->task_name,

            ];

        }
        return response()->json([
            "message" => $this->sendMessageStatus(200),
            "data" => $data,
        ], 200);
    }

    public function changeOwnership($id, $request)
    {
        try {

            \DB::beginTransaction();

            $project = Project::where('id', $id)->first();

            if (!$project) {
                return response()->json([
                    "message" => $this->sendMessageStatus(404),
                ], 404);
            }
            $project->user_id = $request->user_id;
            $project->save();
            $user = \App\Models\User::where('id', $project->user_id)->first();

            $groups = $project->groups;
            foreach ($groups as $group) {
                $group->owner = $user->name;
                $group->owner_id = $user->id;
                $group->save();
            }

            \DB::commit();


            return response()->json([
                "message" => $this->sendMessageStatus(200),
                "project" => $project,
            ], 200);

        } catch (\Exception $e) {
            \DB::rollBack();
            return \response()->json([
                "message" => $this->sendMessageStatus(500),
                'error' => $e->getMessage(),
            ], 500
            );
        }
    }
}

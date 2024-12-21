<?php

namespace App\Repositories\Groups;


use App\Interfaces\Groups\GroupInterface;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Traits\MessagesStatus;
use Illuminate\Support\Facades\Validator;

class GroupRepository implements GroupInterface
{
    use MessagesStatus;

    public function index()
    {

        $groups = Group::where('owner_id', auth('api')->user()->id)->get();


        if ($groups->isEmpty()) {
            return response()->json([
                "error" => $this->sendMessageStatus(404),
                "message" => "Not Have Groups",
            ], 404);
        }

        $data = [
            "Owner" => auth('api')->user()->name, // اسم المالك
            "projects" => [], // قائمة المشاريع
        ];

        foreach ($groups as $group) {
            $data["projects"][] = [
                "project_name" => $group->project_name, // اسم المشروع
                "users" => $group->user_name, // أسماء المستخدمين
                "tasks" => $group->task_name, // أسماء المهام
            ];
        }

        return response()->json([
            "message" => $this->sendMessageStatus(200),
            "data" => $data,
        ], 200);
    }


    public function store($request, $id, $task_id)
    {
        try {
            $data = [];
            $user = User::where('name', $request->name)
                ->where('id', $request->id)->firstOrFail();
            $project = Project::findOrFail($id);
            if ($project) {
                $task = Task::findOrFail($task_id);
                if ($task) {
                    $group = new Group();
                    $group->owner_id = $project->manager->id;
                    $group->owner = $project->manager->name;
                    $group->project_id = $project->id;
                    $group->project_name = $project->name;
                    $group->user_name = $user->name;
                    $group->user_id = $user->id;
                    $group->task_name = $task->name;
                    $group->task_id = $task->id;
                    $group->save();

                    $data[] = [
                        'group_Representative' => $group->id,
                        'group_owner' => $group->owner,
                        'user' => $user->name,
                        'task' => $group->task_name,
                        'project' => $group->project_name

                    ];

                    return response()->json([
                        'message' => $this->sendMessageStatus(201),
                        'data' => $data
                    ], 201);
                }
            }


        } catch (\Exception $r) {
            return response()->json(['message' => $r->getMessage(),
                'error' => $this->sendMessageStatus(500)], 500);
        }
    }

    public function update($request, $group_id, $id, $task_id)
    {
        try {
            $data = [];
            $group = Group::findOrFail($id);
            $project = Project::where('id', $id)->firstOrFail();

            $user = User::where('name', $request->name)
                ->where('id', $request->id)->firstOrFail();


            if ($project) {
                $task = Task::findOrFail($task_id);
                $group->update([
                    'owner_id' => $project->manager->id,
                    'owner' => $project->manager->name,
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'task_name' => $task->name,
                ]);

                $data[] = [
                    'group_Representative' => $group->id,
                    'group_owner' => $group->owner,
                    'user' => $group->user_name,
                    'task' => $group->task_name,
                    'project' => $group->project_name

                ];

                return response()->json([
                    'message' => $this->sendMessageStatus(200),
                    'data' => $data
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $this->sendMessageStatus(500),
                    'error' => $e->getMessage(),
                ], 500
            );
        }
    }

    public function destroy($id)
    {
       $group= Group::findOrFail($id);
       if($group->owner_id==auth('api')->user()->id) {

           $group->delete();
           return response()->json([
               'message' => $this->sendMessageStatus(200),
           ], 200);
       }else{
           return  response()->json(['message'=>'No Authorization'],404);
       }
    }
}

<?php

namespace App\Repositories\Workers;

use App\Interfaces\Workers\WorkerInterface;
use App\Models\Group;
use App\Models\Image;
use App\Models\Task;
use App\Models\User;
use App\Traits\MessagesStatus;
use App\Traits\UploadPhoto;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class  WorkerRepository implements WorkerInterface
{

    use MessagesStatus, UploadPhoto;

    public function index()
    {
      $groups=Group::where('owner_id',auth('api')->user()->id)->get();
      if(!$groups){
          return response()->json([
              "message"=>$this->sendMessageStatus(404),
          ],404);
      }
      $data=[];
      foreach ($groups as $group){
          $task=$group->task;
          $data[]=[
              "Project Name"=>$group->project->name,
              "Responsible person"=>$group->user_name,
              "Contact Person ID "=>$group->user_id,
              "The mission at hand"=>$group->task_name,
              "Task ID"=>$group->task_id,
              "description"=>$task->description,
              "completed task"=>$task->status,
              "complete time"=>optional($task->complete_time),
              "complete description"=>optional($task->complete_description),
          ];
      }
      return response()->json([
          "message"=>$this->sendMessageStatus(200),
          "data"=>$data
      ],200);
    }

    public function store($request)
    {
        try {
            \DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                "complete_description" => ['required', 'string', 'max:255'],

            ]);
            if ($validation->fails()) {
                return response()->json([
                    'message' => $this->sendMessageStatus(422),
                    'error' => $validation->errors()
                ], 422);
            }
            $group = Group::where('user_id', auth('api')
                ->user()->id)->firstOrfail();
            $task = Task::where('id', $group->task_id)->firstOrfail();
            $task->complete_description = $request->complete_description;
            $task->assigned_to = auth('api')->user()->id;
            $task->status = "completed";
            $task->complete_time = now()->format('Y-m-d,h:i:s');
            $task->save();

            $this->uploadImages(
                $request,               // الطلب
                'images',               // اسم الحقل
                $group->project->name.'/'.$task->name,                  // مسار التخزين
                'upload_attachments',   // اسم disk
                $task->id,              // معرف الكيان المرتبط
                'App\Models\Task'       // نوع الكيان المرتبط
            );


            \DB::commit();

            return response()->json([
                "message" => $this->sendMessageStatus(201),
                "task" => $task,
            ], 200);


        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                "message" => $this->sendMessageStatus(500),
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id,$request)
    {
        try {
            \DB::beginTransaction();

            $data=[];
            // التحقق من البيانات
            $validation = Validator::make($request->all(), [
                "complete_description" => ['required', 'string', 'max:255'],

            ]);

            if ($validation->fails()) {
                return response()->json([
                    'message' => $this->sendMessageStatus(422),
                    'error' => $validation->errors()
                ], 422);
            }

            // استرجاع المجموعة والمهمة

            $task=Task::findOrFail($id);
            $group=Group::where('task_id',$task->id)->firstOrFail();

            // تحديث بيانات المهمة
            $task->complete_description = $request->complete_description;
            $task->assigned_to = auth('api')->user()->id;
            $task->status = "completed";
            $task->complete_time = now()->format('Y-m-d H:i:s'); // التنسيق الصحيح
            $task->save();

            // التحقق من وجود صور جديدة
            if ($request->hasFile('images')) {
                $path = $group->project->name.'/'.$task->name;

                // 1. حذف الصور القديمة
                $images = $task->images;
                foreach ($images as $image) {
                    $this->deleteImages($path, $image->filename, "App\Models\Task", $task->id, 'upload_attachments');
                }

                // 2. تحميل الصور الجديدة
                $this->uploadImages(
                    $request,               // الطلب
                    'images',               // اسم الحقل
                    $path,                  // مسار التخزين
                    'upload_attachments',   // اسم disk
                    $task->id,              // معرف الكيان المرتبط
                    'App\Models\Task'       // نوع الكيان المرتبط
                );
            }
            $data[] = [
                "Task Name" => $task->name,
                "Task Identifier" => $task->id,
                "User Make The Task" => optional(User::find($task->assigned_to))->name, // الحصول على اسم المستخدم
                "Project Title" => $group->project->name, // التأكد من وجود المشروع
                "Project Identifier" => optional($group->project)->id,
                "Complete Description" => $task->complete_description,
                "Status" => $task->status,
                "Complete Time" => $task->complete_time,
                "Images" => $task->images->pluck('filename')->toArray(), // جمع أسماء الصور كمصفوفة
            ];


            \DB::commit();

            return response()->json([
                "message" => "Task updated successfully!",
                "data" => $data,

            ], 200);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                "message" => $this->sendMessageStatus(500),
                "error" => $e->getMessage(),
            ], 500);
        }
    }



    public function destroy($id)
    {
        $task=Task::findOrFail($id);
        $task->status="pending";
        $task->assigned_to=null;
        $task->complete_description=null;
        $task->complete_time=null;
        $task->save();
        $group=Group::where('task_id',$task->id)->firstOrFail();
        if($task->images){
                $path = $group->project->name . '/' . $task->name;
                foreach ($task->images as $image) {
                    $this->deleteImages($path, $image->filename, "App\Models\Task", $task->id, 'upload_attachments');
                }

        }

        return response()->json([
            'message'=>$this->sendMessageStatus(200),
        ],200);
    }


    public function showTasks($id)
    {
        // جلب جميع المجموعات الخاصة بالمستخدم بناءً على user_id
        $groups = Group::where('user_id', $id)->with('task')->get();

        $data = [];
        foreach ($groups as $group) {
            $tasks = $group->tasks;
            $data[] = [
                'group' => $group,
                'tasks' => $tasks,
            ];
        }


        return response()->json(
            [
            'message'=>$this->sendMessageStatus(200),
            'data'=>$data
            ],200);
    }

}

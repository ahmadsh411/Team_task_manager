<?php

namespace App\Repositories\Tasks;


use App\Interfaces\Tasks\taskInterface;
use App\Models\Project;
use App\Models\Task;
use App\Traits\MessagesStatus;
use Illuminate\Support\Facades\Validator;

class  taskRepository implements taskInterface
{
    use MessagesStatus;

    public function index()
    {
        // TODO: Implement index() method.
    }

    public function show($id)
    {
       $tasks=Task::where('project_id',$id)->get();
       $data=[];
       foreach ($tasks as $task) {
           $data[]=[
               'id (this is the unique to make all think)'=>$task->id,
               'project_name'=>$task->project->name,
               'task name'=>$task->name,
               'status'=>$task->status,
               'project Owner'=>$task->project->manager->name,
           ];


       }
       return response()->json(
           [
               'message'=>$this->sendMessageStatus(202),
               'data'=>$data
           ],202
       );

    }

    public function store($request, $id)
    {
        try {

            $validation = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:3', // التحقق من الاسم (إلزامي، نص، الطول بين 3 و255 حرفًا)
                'assigned_to' => 'nullable|exists:users,id', // المستخدم المعين (إن وجد) يجب أن يكون موجودًا في جدول المستخدمين
                'title' => 'required|string|max:255|min:5', // التحقق من العنوان مع حد أدنى وأقصى منطقي
                'description' => 'nullable|string|min:15', // الوصف اختياري، ولكنه يجب أن يكون نصًا وبحد أدنى 15 حرفًا إذا وجد
                'status' => 'required|in:pending,in-progress,completed', // الحالة يجب أن تكون واحدة من القيم المحددة
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }
            $project = Project::findOrFail($id);
            $task = new Task();
            $task->name = $request->name;
            $task->project_id = $id;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->title = $request->title;
            $task->save();

            return response()->json([
                'message'=>$this->sendMessageStatus(200)
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status' => $this->sendMessageStatus('500')
            ], 500);
        }
    }

    public function update($request, $id, $task_id)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:3',
                'title' => 'required|string|max:255|min:5',
                'description' => 'nullable|string|min:15',
                'status' => 'required|in:pending,in-progress,completed',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors(),
                    'status' => $this->sendMessageStatus(422)
                ], 422);
            }
            $task = Task::where('id', $task_id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'title' => $request->title,
                'status' => $request->status
            ]);

            return response()->json([
                'message' => $this->sendMessageStatus(201),
                'task' => Task::where('id', $task_id)->first(),
            ],201);


        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status' => $this->sendMessageStatus('500'),
            ], 500);
        }
    }

    public function destroy($id,$task_id)
    {

        $task=Task::where('id',$task_id)->delete();
        return response()->json([
            'message' => $this->sendMessageStatus(200)
        ],200);
    }


}


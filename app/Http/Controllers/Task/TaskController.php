<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Interfaces\Tasks\taskInterface;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $task;

    public function __construct(taskInterface $task)
    {
        $this->task = $task;
        $this->middleware('task_project')->only(['store', 'update', 'destroy', 'show']);
    }

    public function index()
    {
        return $this->task->index();
    }

    public function store(Request $request,$id)
    {
        return $this->task->store($request,$id);
    }

    public function update(Request $request, $id,$task_id)
    {
        return $this->task->update($request, $id,$task_id);
    }

    public function destroy($id,$task_id)
    {
        return $this->task->destroy($id,$task_id);
    }

    #############################show tasks on project ######################################

    public function show($id){
        return $this->task->show($id);
    }
}

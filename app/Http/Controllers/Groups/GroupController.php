<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Interfaces\Groups\GroupInterface;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $group;

    public function __construct(GroupInterface $group)
    {
        $this->group = $group;
        $this->middleware('groupCreate')->only('store','update');
        $this->middleware('operation_group')->only(['destroy','index']);
    }

    public function index()
    {

        return $this->group->index();
    }

    public function store(Request $request,  $id,  $task_id)
    {
        return $this->group->store($request, $id, $task_id);
    }

    public function update(Request $request, $id,$task_id,$group_id)
    {
        return $this->group->update($request,$group_id,$id,$task_id);
    }

    public function destroy($id)
    {
        return $this->group->destroy($id);
    }
}

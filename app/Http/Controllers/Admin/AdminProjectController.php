<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\AdminInterface;
use Illuminate\Http\Request;

class AdminProjectController extends Controller
{
    protected $project;

    public function __construct(AdminInterface $project)
    {
        $this->project = $project;
    }


    public function index()
    {
        return $this->project->index();
    }

    public function destroyProject($id)
    {
        return $this->project->destroyProject($id);
    }

    public function destroyTask($id)
    {

        return $this->project->destroyTask($id);
    }

    public function destroyGroup($id)
    {

        return $this->project->destroyGroup($id);

    }

    public function showTasks()
    {

        return $this->project->showTasks();
    }

    public function showGroups()
    {

        return $this->project->showGroups();
    }

    public function changeOwnerShip($id, Request $request)
    {

        return $this->project->changeOwnership($id, $request);

    }




}

<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Interfaces\Projects\projectInterface;
use App\Models\Project;
use App\Traits\MessagesStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    use MessagesStatus;

    protected $projectService;

    public function __construct(projectInterface $projectService)
    {
        $this->projectService = $projectService;
        $this->middleware('user_project')->only(['update', 'destroy']);
    }

    public function index()
    {
        return $this->projectService->index();
    }

    public function show($id)
    {

    }

    public function store(Request $request)
    {
       return $this->projectService->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->projectService->update($request, $id);
    }

    public function destroy($id)
    {
         return $this->projectService->destroy($id);
    }

}

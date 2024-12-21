<?php

namespace App\Http\Controllers\Workers;

use App\Http\Controllers\Controller;
use App\Interfaces\Workers\WorkerInterface;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    protected $jops;

    public function __construct(WorkerInterface $jops)
    {
        $this->jops = $jops;
        $this->middleware(['completed_task'])->only(['store']);
        $this->middleware(['operation_task'])->only(['update','destroy']);
        $this->middleware(['showTask'])->only('show');
    }

    public function index()
    {

        return $this->jops->index();
    }

    public function store(Request $request)
    {

        return $this->jops->store($request);
    }

    public function update($id, Request $request)
    {
        return $this->jops->update($id,$request);
    }

    public function destroy($id)
    {
        return $this->jops->destroy($id);
    }

//    #########Route And MiddleWare
    public  function show($id){

        return $this->jops->showTasks($id);
    }

}

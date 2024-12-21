<?php

namespace  App\Interfaces\Workers;

interface WorkerInterface  {

    public function index();

    public function store($request);


    public function update($id,$request);


    public function destroy($id);

    public  function  showTasks($id);
}

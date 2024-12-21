<?php

namespace App\Interfaces\Admin;

interface AdminInterface
{

    public  function  index();

    public  function store($request);

    public function update($id,$request);

    public function destroyTask($id);

    public function destroyProject($id);

    public function destroyGroup($id);

    public function showTasks();

    public function showGroups();


    public function  changeOwnership($id,$request);





}

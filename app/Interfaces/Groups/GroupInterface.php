<?php
namespace App\Interfaces\Groups;

interface GroupInterface {

    public function index();

    public  function store($request,$id,$task_id);

    public function update($request,$group_id,$id,$task_id);

    public function  destroy($id);
}

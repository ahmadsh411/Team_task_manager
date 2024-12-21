<?php

namespace App\Interfaces\Tasks;

interface taskInterface{

    public function index();

    public function show($id);

    public function store($request,$id);

    public function update($request, $id,$task_id);

    public function destroy($id,$task_id);
}

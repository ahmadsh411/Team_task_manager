<?php
namespace App\Interfaces\Projects;

use http\Env\Request;

interface projectInterface{
    public function index();

    public function show($id);

    public function create();

    public function store( $request);

    public function edit($id);
    public function update($request, $id);

    public function destroy($id);

}

<?php

namespace App\Repositories\Projects;

use App\Interfaces\Projects\projectInterface;
use App\Models\Project;
use App\Traits\MessagesStatus;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class projectRepositroy implements projectInterface
{
    use MessagesStatus;

    public function index()
    {
        // TODO: Implement index() method.
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function store($request)
    {
        try {
            $user=auth('api')->user();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:3',
                'description' => 'required|string|max:255|min:12',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),
                    'message' => $this->sendMessageStatus(400)], 400);
            }
            if (!auth('api')->check()) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }



            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => $this->sendMessageStatus(200),
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),
                "status" => $this->sendMessageStatus(500)], 500);
        }
    }

    public function edit($id)
    {
        // TODO: Implement edit() method.
    }

    public function update($request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:3',
                'description' => 'required|string|max:255|min:12',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),
                    'message' => $this->sendMessageStatus(400)], 400);
            }
            $project = Project::findOrFail($id);
            $project->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'user_id'=>auth('api')->user()->id,
            ]);
            return response()->json([
                'message' => $this->sendMessageStatus(201),
                'project' => $project,

            ],201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 500]);
        }
    }

    public function destroy($id){
        $project=Project::findOrFail($id);
        $project->delete();
        return response()->json([
            'message'=>$this->sendMessageStatus(200),
        ],200);
    }
}

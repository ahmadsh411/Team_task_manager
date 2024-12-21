<?php

use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Groups\GroupController;
use App\Http\Controllers\Workers\WorkerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['middleware' => 'guest'], function () {
    //#################################################User Authentication#######################

    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
//    ###############forget password###############################################################
    Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);

});
//#######################Logout User#######################################################################
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:api');


Route::group(['middleware' => 'auth:api'], function () {


//    ############################Projects##########################################################################


    Route::post('create-project', [ProjectController::class, 'store']);

    Route::post('update-project/{id}', [ProjectController::class, 'update'])
        ->middleware('user_project');

    Route::delete('delete-project/{id}', [ProjectController::class, 'destroy'])
        ->middleware('user_project');

//####################################Tasks##########################################################################

    Route::post('create-task/{id}', [TaskController::class, 'store'])
        ->middleware('task_project');

    Route::post('update-task-in/{id}/{task_id}', [TaskController::class, 'update'])
        ->middleware('task_project');

    Route::delete('delete-task-in/{id}/{task_id}', [TaskController::class, 'destroy'])
        ->middleware('task_project');

    Route::get('show-task-in/{id}', [TaskController::class, 'show'])
        ->middleware('task_project');
//    #################################################################Groups_project###################################

    Route::post('create-group-to-project/{id}/add-task/{task_id}', [GroupController::class, 'store'])
        ->middleware('groupCreate');

    Route::post('update-group-to-project/{id}/update-task/{task_id}/group/{group_id}', [GroupController::class, 'update'])
        ->middleware('groupCreate');

    Route::delete('delete-group/{id}', [GroupController::class, 'destroy'])
    ->middleware('operation_group');

    Route::get('group-project', [GroupController::class, 'index'])
    ->middleware('operation_group');

    //    ########################################show My GroupProject_task#######################################################

    Route::get('my-group-tasks', [WorkerController::class, 'index']);


});

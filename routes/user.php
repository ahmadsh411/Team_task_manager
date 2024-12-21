<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Workers\WorkerController;

Route::middleware('auth:api')->group(function (){

//################################################complete Tasks##############################################

    Route::post('complete-task',[WorkerController::class,'store'])
    ->middleware('completed_task');

    Route::post('update-complete_task/{id}',[WorkerController::class,'update'])
    ->middleware('operation_task');

    Route::delete('delete-completed-task/{id}',[WorkerController::class,'destroy'])
        ->middleware('operation_task');

    Route::get('show-task/{id}',[WorkerController::class,'show'])
        ->middleware('showTask');
});

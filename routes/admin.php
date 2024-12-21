<?php

use App\Http\Controllers\Auth\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminProjectController;


Route::prefix('admin')->group(function () {

    Route::post('register', [AdminController::class, 'register'])->middleware('guest');

    Route::post('login', [AdminController::class, 'login'])->middleware('guest');

    Route::post('logout', [AdminController::class, 'logout'])->middleware('auth:admin');
//    ############################################################forget_password#############################
    Route::post('forget-password', [AdminController::class, 'forgetPassword'])->middleware('guest');

    Route::post('reset-password', [AdminController::class, 'resetPassword'])->middleware('guest');

//    ##################################################################Project#########################################

    Route::middleware('auth:admin')->group(function () {

        Route::get('all-projects', [AdminProjectController::class, 'index']);

//########################################################Testing##################################################
        Route::delete('delete-project/{id}', [AdminProjectController::class, 'destroyProject']);

        Route::delete('delete-group/{id}', [AdminProjectController::class, 'destroyGroup']);

        Route::delete('delete-task/{id}', [AdminProjectController::class, 'destroyTask']);

        Route::get('all-tasks', [AdminProjectController::class, 'showTasks']);

        Route::get('all-groups', [AdminProjectController::class, 'showGroups']);

        Route::post('change-ownership/{id}',[AdminProjectController::class,'changeOwnerShip']);

    });


});

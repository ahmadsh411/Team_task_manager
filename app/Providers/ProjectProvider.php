<?php

namespace App\Providers;

use App\Interfaces\Admin\AdminInterface;
use App\Interfaces\Groups\GroupInterface;
use App\Interfaces\Projects\projectInterface;
use App\Interfaces\Tasks\taskInterface;
use App\Interfaces\Workers\WorkerInterface;
use App\Repositories\Admin\AdminRepository;
use App\Repositories\Groups\GroupRepository;
use App\Repositories\Projects\projectRepositroy;
use App\Repositories\Tasks\taskRepository;
use App\Repositories\Workers\WorkerRepository;
use Illuminate\Support\ServiceProvider;

class ProjectProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(projectInterface::class, projectRepositroy::class);
        $this->app->bind(taskInterface::class,taskRepository::class);
        $this->app->bind(GroupInterface::class,GroupRepository::class);
        $this->app->bind(WorkerInterface::class,WorkerRepository::class);
        $this->app->bind(AdminInterface::class,AdminRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

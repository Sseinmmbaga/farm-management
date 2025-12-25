<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Tasks\Task;
use App\Models\Tasks\Labor;
use App\Models\User;
use App\Policies\FarmPolicy;
use App\Policies\FieldPolicy;
use App\Policies\TaskPolicy;
use App\Policies\LaborPolicy;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Farm::class, FarmPolicy::class);
        Gate::policy(Field::class, FieldPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Labor::class, LaborPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}

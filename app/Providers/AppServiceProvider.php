<?php

namespace App\Providers;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EntityCoreController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $entity = new EntityCoreController;
        View::share('entity', $entity->entity);

        View::composer('backend.*', function ($view) {
            $admin = new AdminController();
            $view->with('admin', $admin->admin);
        });
    }
}

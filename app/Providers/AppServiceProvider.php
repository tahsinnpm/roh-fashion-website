<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use App\Models\Category;


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
        Schema::defaultStringLength(191);
        View::composer('components.mobile-navigation', function ($view) {
            $view->with('mobileCategories', Category::where('status', 1)->orderBy('name_en')->get(['id', 'parent_id', 'name_en', 'name_bn', 'slug'])->groupBy('parent_id'));
        });
        View::composer('FrontEnd.include.header', function ($view){
            $view->with(
                'categories', Category::orderBy('name_en','ASC')->where('status','=',1)->limit(11)->get()
            );

        });

    }
}

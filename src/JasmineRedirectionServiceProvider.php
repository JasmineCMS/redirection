<?php

namespace Jasmine\Redirection;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Jasmine\Jasmine\Facades\Jasmine;
use Jasmine\Redirection\Http\Controllers\RedirectionController;

class JasmineRedirectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        
        Jasmine::registerAuthenticatedRouteGroup(function () {
            Route::controller(RedirectionController::class)->prefix('redirection')->group(function () {
                Route::get('/', 'index')->name('redirection.index');
                Route::post('/', 'save');
                Route::delete('/', 'delete');
                Route::get('/export', 'export')->name('redirection.export');
                Route::post('/import', 'import')->name('redirection.import');
                Route::delete('/purge', 'purge')->name('redirection.purge');
            });
        }, 'jasmine-redirection');
        
        Jasmine::registerSideBarSubMenuItem('tools', 'redirections', function () {
            return [
                'href'     => route('jasmine.redirection.index'),
                'is-route' => 'jasmine.redirection.index',
                'title'    => __('Redirections'),
            ];
        });
    }
    
    public function boot()
    {
    
    }
}
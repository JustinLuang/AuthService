<?php


namespace Alamb\AuthService;


use Illuminate\Config\Repository;
use Alamb\AuthService\AuthService;

use Illuminate\Support\ServiceProvider;

class AuthProvider extends ServiceProvider{
	public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/config/service.php' => config_path('service.php'),
        ]);
    }
	
	
	 public function register()
    {
        $this->app->singleton('authService', function ($app) {
            return AuthService::getInstance();
        });
    }
}

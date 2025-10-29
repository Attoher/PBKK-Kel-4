<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Keep empty or add global middleware classes as needed.
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [],
        'api' => [],
    ];

    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // Register custom middleware aliases here
        'check.python' => \App\Http\Middleware\CheckPythonEnv::class,
    ];
    
    /**
     * Newer Laravel versions use middlewareAliases property for route middleware aliases.
     * Keep both in sync to support different framework versions.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'check.python' => \App\Http\Middleware\CheckPythonEnv::class,
    ];
}

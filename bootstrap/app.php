<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            // Error code 23503 is for PostgreSQL and 1451 is for MySQL Foreign Key Violations
            if ($e->getCode() == 23503 || $e->getCode() == 1451) {
                return redirect()->back()->with('sweet_error', 'No se puede eliminar este registro porque está siendo utilizado por otros módulos del sistema.');
            }
        });
    })->create();

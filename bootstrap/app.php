<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // SESSION AUTH (SPA same-domain only): enables CSRF + session on API routes
        // $middleware->statefulApi();
        // $middleware->api(prepend: [
        //     \Illuminate\Session\Middleware\StartSession::class,
        // ]);
        // revoker les comptes suspendus
        $middleware->alias([
            'not.suspended' => \App\Http\Middleware\CheckNotSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Gérer erreurs auth pour API (retourner JSON 401)
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Non authentifié.',
                ], 401);
            }
            return redirect()->guest(route('login'));
        });

        // Fichier trop volumineux (dépasse upload_max_filesize PHP)
        $exceptions->render(function (PostTooLargeException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Le fichier est trop volumineux. Taille maximale autorisée : 5 Mo.',
                ], 422);
            }
        });
    })
    // ->withMiddleware(function (Middleware $middleware) {
    //     $middleware->alias([
    //         'not.suspended' => \App\Http\Middleware\CheckNotSuspended::class,
    //     ]);
    // })
    ->create();
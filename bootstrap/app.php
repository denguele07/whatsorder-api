<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Middleware API : activer Sanctum SPA

        // Ce middleware détecte si la requête vient d'un domaine listé dans
        // SANCTUM_STATEFUL_DOMAINS. Si oui, il active les sessions (cookies).
        // Sinon, il laisse passer en mode stateless (tokens).
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);


        // Redirection auth pour les routes non-API

        $middleware->redirectGuestsTo(function ($request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return route('login');
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Forcer le format JSON pour les routes /api/*
        $exceptions->shouldRenderJsonWhen(function ($request, $throwable) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // RUSUNAWA_API_ERRORS
        $exceptions->shouldRenderJsonWhen(fn ($request, $e) => $request->is('api/*') || $request->expectsJson());
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (!$request->is('api/*')) return null;
            $status=500; $message='Kesalahan server.'; $extra=[]; $headers=[];
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $status=422; $message='Validasi gagal.'; $extra=['errors'=>$e->errors()];
            } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                $status=401; $message='Token tidak valid atau belum login.';
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status=$e->getStatusCode(); $headers=$e->getHeaders();
                $message=match($status) { 401=>'Belum login.',403=>'Akses ditolak.',404=>'Data tidak ditemukan.',405=>'Metode tidak diizinkan.',409=>$e->getMessage() ?: 'Konflik data.',429=>'Terlalu banyak permintaan.',default=>'Permintaan gagal.' };
            }
            return response()->json(['status'=>'error','message'=>$message,'data'=>null]+$extra,$status,$headers);
        });
        //
    })->create();

<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        $this->renderable(function (RouteNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                $response = [
                    'message' => $e->getMessage()
                ];
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Terjadi kesalahan server. Silakan coba lagi nanti.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

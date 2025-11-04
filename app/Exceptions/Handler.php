<?php

namespace App\Exceptions;

use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{   
    public function render($request, Throwable $e)
{
    return response()->json([
        'handler' => true,
        'exception' => get_class($e),
        'message' => $e->getMessage(),
    ], 500);
}
    // public function render($request, Throwable $e)
    // {   
        
    //     // Spatie Permission Exception
    //     if ($e instanceof SpatieUnauthorizedException) {
    //         return response()->json([
    //             'status' => false,
    //             'error' => 'PERMISSION_DENIED',
    //             'message' => 'You do not have permission to perform this action.',
    //         ], 403);
    //     }

    //     // Laravel Authorization / Gate Exception
    //     if ($e instanceof AccessDeniedHttpException) {
    //         return response()->json([
    //             'status' => false,
    //             'error' => 'ACCESS_DENIED',
    //             'message' => 'Access denied.',
    //         ], 403);
    //     }

    //     return parent::render($request, $e);
    }

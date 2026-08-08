<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'error' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => array_slice(array_map(function($t) {
                return [
                    'file' => isset($t['file']) ? $t['file'] : null,
                    'line' => isset($t['line']) ? $t['line'] : null,
                    'function' => isset($t['function']) ? $t['function'] : null,
                    'class' => isset($t['class']) ? $t['class'] : null,
                ];
            }, $e->getTrace()), 0, 10),
        ], JSON_PRETTY_PRINT);
        exit;
    }
}

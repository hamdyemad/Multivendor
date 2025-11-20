<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use App\Traits\Res;

class Handler extends ExceptionHandler
{
    use Res;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into a response.
     */
    public function render($request, Throwable $e)
    {
        // Handle model not found exceptions
        if ($e instanceof ModelNotFoundException) {
            $message = config('responses.not_found')[app()->getLocale()] ?? 'Resource not found';
            if ($request->expectsJson()) {
                return $this->sendRes($message, false, [], [], 404);
            }
        }

        // Handle validation exceptions
        if ($e instanceof ValidationException) {
            if ($request->expectsJson()) {
                return $this->sendRes(config('responses.validation')[app()->getLocale()] ?? 'Validation failed', false, [], $e->errors(), 422);
            }
        }

        // Handle database exceptions
        if ($e instanceof QueryException) {
            \Illuminate\Support\Facades\Log::error('Database error: ' . $e->getMessage(), [
                'query' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($request->expectsJson()) {
                $message = config('responses.database_error')[app()->getLocale()] ?? 'Database error occurred';
                $data = app()->isLocal() ? ['error' => $e->getMessage()] : [];
                return $this->sendRes($message, false, $data, [], 500);
            }
        }

        // Handle HTTP exceptions
        if ($e instanceof HttpException) {
            if ($request->expectsJson()) {
                $message = $e->getMessage() ?: config('responses.http_error')[app()->getLocale()] ?? 'HTTP error occurred';
                return $this->sendRes($message, false, [], [], $e->getStatusCode());
            }
        }

        // Handle generic exceptions
        if ($request->expectsJson() && !($e instanceof ValidationException)) {
            \Illuminate\Support\Facades\Log::error('Unhandled exception: ' . $e->getMessage(), [
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $data = [
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
            $message = config('responses.error')[app()->getLocale()] ?? 'An error occurred';
            return $this->sendRes($message, false, $data, [], 500);
        }

        return parent::render($request, $e);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

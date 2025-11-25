<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use App\Traits\Res;
use App\Exceptions\InvalidPasswordException;
use Illuminate\Support\Facades\DB;

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
     * Safely rollback any active database transaction
     */
    private function safeRollback(): void
    {
        try {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            // Silently ignore rollback errors
        }
    }

    /**
     * Render an exception into a response.
     */
    public function render($request, Throwable $e)
    {
        // Rollback transactions for errors that need it
        if ($e instanceof HttpException ||
            ($e instanceof \Throwable && !($e instanceof ValidationException))) {
            $this->safeRollback();
        }

        // Handle authentication exceptions
        if ($e instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                $message = config('responses.unauthorized')[app()->getLocale()] ?? 'Unauthorized';
                return $this->sendRes($message, false, [], [], 401);
            }
        }

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

        if (!$request->expectsJson()) {
            return parent::render($request, $e);
        }

        // Match exception type and handle accordingly
        return match (true) {
            $e instanceof QueryException => $this->handleDatabaseException($e),
            $e instanceof InvalidPasswordException => $this->sendRes($e->getMessage(), false, [], [], 422),
            $e instanceof HttpException => $this->handleHttpException($e),
            $e instanceof \Symfony\Component\Mime\Exception\LogicException,
            $e instanceof \Symfony\Component\Mailer\Exception\TransportException => $this->handleMailException(),
            $e instanceof ValidationException => parent::render($request, $e),
            default => $this->handleGenericException($e),
        };
    }

    /**
     * Handle database exceptions
     */
    private function handleDatabaseException(QueryException $e)
    {
        \Illuminate\Support\Facades\Log::error('Database error: ' . $e->getMessage(), [
            'query' => $e->getSql() ?? 'N/A',
            'bindings' => $e->getBindings() ?? [],
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        $message = config('responses.database_error')[app()->getLocale()] ?? 'Database error occurred';
        $data = app()->isLocal() ? ['error' => $e->getMessage()] : [];
        return $this->sendRes($message, false, $data, [], 500);
    }

    /**
     * Handle HTTP exceptions
     */
    private function handleHttpException(HttpException $e)
    {
        $message = $e->getMessage() ?: config('responses.http_error')[app()->getLocale()] ?? 'HTTP error occurred';
        return $this->sendRes($message, false, [], [], $e->getStatusCode());
    }

    /**
     * Handle mail sending exceptions
     */
    private function handleMailException()
    {
        $message = config('responses.email_send_failed')[app()->getLocale()] ?? 'Could not send email. Please try again later.';
        return $this->sendRes($message, false, [], [], 500);
    }

    /**
     * Handle generic exceptions
     */
    private function handleGenericException(\Throwable $e)
    {
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

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

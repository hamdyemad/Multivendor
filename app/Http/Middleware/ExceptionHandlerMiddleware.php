<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Traits\Res;

class ExceptionHandlerMiddleware
{
    use Res;

    /**
     * Handle an incoming request and catch all exceptions
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e, $request);
        } catch (ModelNotFoundException $e) {
            return $this->handleModelNotFoundException($e, $request);
        } catch (QueryException $e) {
            return $this->handleQueryException($e, $request);
        } catch (HttpException $e) {
            return $this->handleHttpException($e, $request);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e, $request);
        }
    }

    private function handleValidationException(ValidationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return $this->sendRes(config('responses.validation')[app()->getLocale()], false, [], $e->errors(), 422);
        }

        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    private function handleModelNotFoundException(ModelNotFoundException $e, Request $request)
    {        
        $message = config('responses.not_found')[app()->getLocale()];
        if ($request->expectsJson()) {
            return $this->sendRes($message, false, [], [], 404);
        }

        abort(404, $message);
    }

    private function handleQueryException(QueryException $e, Request $request)
    {
        \Illuminate\Support\Facades\Log::error('Database error: ' . $e->getMessage(), [
            'query' => $e->getSql() ?? 'N/A',
            'bindings' => $e->getBindings() ?? [],
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        $message = config('responses.Database error occurred')[app()->getLocale()];
        if ($request->expectsJson()) {
            $data = app()->isLocal() ? ['error' => $e->getMessage()] : [];
            return $this->sendRes($message, false, $data, [], 500);
        }

        return abort(500, $message);
    }

    private function handleHttpException(HttpException $e, Request $request)
    {
        if ($request->expectsJson()) {
            $message = $e->getMessage() ?: Config('responses.HTTP error occurred')[app()->getLocale()];
            return $this->sendRes($message, false, [], [], $e->getStatusCode());
        }

        throw $e;
    }

    private function handleGeneralException(\Exception $e, Request $request)
    {
        \Illuminate\Support\Facades\Log::error('Unhandled exception: ' . $e->getMessage(), [
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        if ($request->expectsJson()) {
            $data = app()->isLocal() ? ['error' => $e->getMessage()] : [];
            return $this->sendRes(config('responses.error')[app()->getLocale()], false, $data, [], 500);
        }

        if (app()->isLocal()) {
            throw $e;
        }

        return abort(500, config('responses.error')[app()->getLocale()]);
    }
}

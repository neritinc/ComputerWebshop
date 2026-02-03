<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class Controller
{
    protected function apiResponse(callable $callback)
    {
        try {
            $result = $callback();

            return response()->json([
                'message' => 'OK',
                'data' => $result
            ], 200, options: JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validációs hiba történt.',
                'errors' => $e->errors(),
                'data' => null
            ], 422, options: JSON_UNESCAPED_UNICODE);

        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null,
            ], 403, options: JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            $status = 500;
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
            }
            if ($e instanceof ModelNotFoundException) {
                $status = 404;
            }

            return response()->json([
                'message' => config('app.debug') ? $e->getMessage() : 'Váratlan hiba történt.',
                'data' => null
            ], $status, options: JSON_UNESCAPED_UNICODE);
        }
    }
}


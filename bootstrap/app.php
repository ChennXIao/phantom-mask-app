<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\MaskNotFoundInPharmacyException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InsufficientFundsException;
use App\Enums\ApiStatusCodeEnum;
use Illuminate\Validation\ValidationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',

        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'metadata' => [
                    'status' => ApiStatusCodeEnum::VALIDATION_ERROR->value,
                    'message' => $e->getMessage() ?: ApiStatusCodeEnum::VALIDATION_ERROR->message(),
                ],
                'data' => null,
            ], 422);
        });

        $exceptions->map(function (ModelNotFoundException $e) {
            return new NotFoundHttpException($e->getMessage(), $e);
        });

        $exceptions->render(function (MaskNotFoundInPharmacyException $e, $request) {
            return response()->json([
                'metadata' => [
                    'status' => ApiStatusCodeEnum::MASK_NOT_FOUND_IN_PHARMACY->value,
                    'message' => $e->getMessage(),
                ],
                'data' => null,
            ], 404);
        });

        $exceptions->render(function (InsufficientStockException $e, $request) {
            return response()->json([
                'metadata' => [
                    'status' => ApiStatusCodeEnum::INSUFFICIENT_STOCK->value,
                    'message' => ApiStatusCodeEnum::INSUFFICIENT_STOCK->message(),
                ],
                'data' => null,
            ], 422);
        });

        $exceptions->render(function (InsufficientStockException $e, $request) {
            return response()->json([
                'metadata' => [
                    'status' => ApiStatusCodeEnum::INSUFFICIENT_STOCK->value,
                    'message' => ApiStatusCodeEnum::INSUFFICIENT_STOCK->message(),
                ],
                'data' => null,
            ], 422);
        });

        $exceptions->render(function (InsufficientFundsException $e, $request) {
            return response()->json([
                'metadata' => [
                    'status' => ApiStatusCodeEnum::INSUFFICIENT_CASH->value,
                    'message' => ApiStatusCodeEnum::INSUFFICIENT_CASH->message(),
                ],
                'data' => null,
            ], 422);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious();

            $status = ApiStatusCodeEnum::RESOURCE_NOT_FOUND;
            $message = $status->message();
            if ($previous instanceof ModelNotFoundException) {
                $status = ApiStatusCodeEnum::MODEL_NOT_FOUND;
                $model = class_basename($previous->getModel());
                dd($model, $previous->getIds(), $previous->getMessage());
                $ids = implode(',', $previous->getIds());
                $message = "{$model} with ID {$ids} not found";
            }

            return new JsonResponse([
                'metadata' => [
                    'status' => $status->value,
                    'message' => $message,
                ],
                'data' => null,
            ], 404);
        });
    })->create();

<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InsufficientStockException extends CustomException
{
    public function __construct(string $message = "Insufficient stock for the requested mask.", Throwable $previous = null)
    {
        parent::__construct($message, '4221', Response::HTTP_UNPROCESSABLE_ENTITY, null, $previous);
    }
}

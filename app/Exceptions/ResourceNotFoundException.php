<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceNotFoundException extends CustomException
{
    public function __construct(string $message = "The requested resource could not be found.", string $apiCode = "4040", int $httpStatusCode = Response::HTTP_NOT_FOUND, $data = null, Throwable $previous = null)
    {
        parent::__construct($message, $apiCode, $httpStatusCode, $data, $previous);
    }
}

<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class MaskNotFoundInPharmacyException extends CustomException
{
    public function __construct(string $message = "The specified mask was not found in this pharmacy.", Throwable $previous = null)
    {
        parent::__construct($message, '4041', Response::HTTP_NOT_FOUND, null, $previous);
    }
}

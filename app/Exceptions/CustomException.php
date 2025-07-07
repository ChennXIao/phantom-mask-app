<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CustomException extends Exception
{
    protected $apiCode;
    protected $httpStatusCode;
    protected $data;

    public function __construct(string $message = "", string $apiCode = "5000", int $httpStatusCode = 500, $data = null, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->apiCode = $apiCode;
        $this->httpStatusCode = $httpStatusCode;
        $this->data = $data;
    }

    public function getApiCode(): string
    {
        return $this->apiCode;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getData()
    {
        return $this->data;
    }
}

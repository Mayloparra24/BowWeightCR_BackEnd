<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class WeightEstimationException extends Exception
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly ?Response $response = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}

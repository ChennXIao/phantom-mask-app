<?php
namespace App\Enums;

enum ApiStatusCodeEnum: string
{
    case SUCCESS = '0000';
    case VALIDATION_ERROR = '4000';
    case RESOURCE_NOT_FOUND = '4040';
    case MASK_NOT_FOUND_IN_PHARMACY = '4041';
    case REQUEST_UNPROCESSABLE = '4220';
    case INSUFFICIENT_STOCK = '4221';
    case INSUFFICIENT_CASH = '4222';
    case MODEL_NOT_FOUND = '4042';
    case INTERNAL_SERVER_ERROR = '5000';

    public function message(): string
    {
        return config('api_code.' . $this->value, 'Unknown error');
    }
}

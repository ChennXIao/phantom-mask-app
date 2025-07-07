<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Status Code
    |--------------------------------------------------------------------------
    |
    | A centralized list of all API status codes and default messages.
    |
    */

    // Success Codes (0000-0999)
    '0000' => 'Success.',

    // Client Error Codes (4000-4999)
    '4000' => 'A validation error occurred.',
    '4040' => 'The requested resource could not be found.',
    '4041' => 'The specified mask was not found in this pharmacy.',
    '4220' => 'The request could not be processed.',
    '4221' => 'Insufficient stock for the requested mask.',
    '4222' => 'Insufficient cash balance for the transaction.',

    // Server Error Codes (5000-5999)
    '5000' => 'An internal server error occurred.',
];

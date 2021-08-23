<?php

use Illuminate\Support\Facades\Route;

/**
 * Return the common simple message response for the given message and the status code.
 *
 * @param $message
 * @param int $status
 * @return JsonResponse
 */
function simpleMessageResponse($message, $status = SUCCESS)
{
    return response()->json([
        'code' => $status,
        'message' => $message,
    ], $status);
}

/**
 * Common error log method for logging errors with a custom format
 *
 * @param $message
 * @param $location
 * @param null $errorObject
 * @param array $reference
 */
function logError($message, $location, $errorObject = null, array $reference = [])
{
    \Log::error([
        $message => [
            'location' => $location,
            'route' => currentRouteName(),
            'message' => $errorObject->getMessage(),
            'reference' => $reference,
            'trace' => $errorObject->getTraceAsString(),
        ],
    ]);
}


/**
 * Get the current route name.
 *
 * @return string|null
 */
function currentRouteName(): ?string
{
    return Route::currentRouteName();
}

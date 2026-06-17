<?php

namespace App\Exceptions;

/**
 * Deprecated compatibility class.
 *
 * This file previously contained a custom exception handler that sent
 * error notifications. The application now uses the default
 * `App\Exceptions\Handler`. This class exists only to avoid fatal
 * errors if referenced elsewhere; it simply extends the default
 * handler.
 */
class ErrorExceptionHandler extends Handler
{
    // Intentionally empty — use App\Exceptions\Handler instead.
}

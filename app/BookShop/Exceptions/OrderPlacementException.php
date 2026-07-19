<?php

namespace App\BookShop\Exceptions;

use Exception;

/**
 * Thrown for expected, user-facing order placement failures (no branch
 * covers the customer's region, insufficient stock, empty cart, etc.) —
 * distinct from unexpected errors so controllers can catch this
 * specifically and show the message directly rather than a generic 500.
 */
class OrderPlacementException extends Exception
{
    //
}

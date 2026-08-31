<?php

namespace App\BookShop\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Deliberately does not extend App\Http\Controllers\Controller — keeps
 * the module's controller tree independent of the host app.
 */
class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
}

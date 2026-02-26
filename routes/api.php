<?php

use App\Message\ActionRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/{action}', function (Request $request, ActionRegistry $registry, string $action) {
    return $registry->getHandler($action)?->handleRequest($request) ?? abort(404);
});

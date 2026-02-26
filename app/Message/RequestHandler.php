<?php

namespace App\Message;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface RequestHandler
{
    public function handleRequest(Request $request): JsonResponse;
}

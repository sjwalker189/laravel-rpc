<?php

namespace App\Message;

use Illuminate\Support\Arr;

class ActionRegistry
{
    public function getHandler(string $name): ?RequestHandler
    {
        $action = Arr::get(config('rpc.actions'), $name);
        if ($action != null) {
            return app($action);
        }

        return null;
    }
}

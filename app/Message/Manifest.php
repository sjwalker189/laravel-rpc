<?php

namespace App\Message;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class Manifest
{
    // This class is intentionally left blank.
    // It exists solely to trigger the typescript transformer.
    public array $actions = [
        'ping' => \App\Message\Ping\PingAction::class,
    ];
}

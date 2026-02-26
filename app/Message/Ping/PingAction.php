<?php

namespace App\Message\Ping;

use App\Message\IsAction;
use App\Message\RequestHandler;

class PingAction implements RequestHandler
{
    use IsAction;

    public function handle(PingInput $input): PingInput
    {
        return new PingInput(
            message: $input->message,
        );
    }
}

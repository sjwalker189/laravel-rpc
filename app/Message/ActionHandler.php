<?php

namespace App\Message;

abstract class ActionHandler implements RequestHandler
{
    use IsAction;
}

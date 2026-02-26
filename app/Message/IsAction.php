<?php

namespace App\Message;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;

trait IsAction
{
    public function handleRequest(Request $request): JsonResponse
    {
        try {
            $result = $this->invoke($request->all());

            return response()->json([
                'data' => $result,
            ]);
        } catch (ValidationException $e) {
            return response()->json($e->validator->getMessageBag()->toArray(), 422);
        }
    }

    private function invoke($input)
    {
        $reflection = new ReflectionMethod($this, 'handle');
        $parameters = $reflection->getParameters();

        if (empty($parameters)) {
            return $this->handle();
        }

        // TODO: Identify if LaravelData class
        $inputParameter = $parameters[0];
        $inputClassName = $inputParameter->getType()->getName();
        $inputInstance = $inputClassName::validateAndCreate($input);

        return $this->handle($inputInstance);
    }
}

<?php

namespace App\Message;

use ReflectionClass;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;

class ActionTransformer implements Transformer
{
    public function transform(ReflectionClass $class, string $name): ?TransformedType
    {
        if (! $class->hasMethod('handle')) {
            return null;
        }

        $method = $class->getMethod('handle');

        $parameters = $method->getParameters();
        $inputParam = $parameters[0] ?? null;

        $inputType = $this->getTypeName($inputParam?->getType());
        $returnType = $this->getTypeName($method->getReturnType());

        $typescript = "{ input: {$inputType}, output: {$returnType} }";

        return TransformedType::create(
            $class,
            $class->getShortName(),
            $typescript,
        );
    }

    private function getTypeName(mixed $type): string
    {
        if (! $type) {
            return 'any';
        }

        $name = $type->getName();

        // Map PHP types to TypeScript primitives
        return match ($name) {
            'string' => 'string',
            'int', 'float' => 'number',
            'bool' => 'boolean',
            'void' => 'void',
            default => (new ReflectionClass($name))->getShortName(),
        };
    }
}

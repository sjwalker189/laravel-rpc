<?php

namespace App\Message;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;

class ManifestTransformer implements Transformer
{
    public function transform(ReflectionClass $class, string $name): ?TransformedType
    {
        // Only trigger this logic for our specific anchor class
        if ($class->getName() !== 'App\Message\Manifest') {
            return null;
        }

        $actions = $class->getProperty('actions')->getDefaultValue();

        $unionMembers = [];

        foreach ($actions as $actionName => $actionClass) {
            $unionMembers[] = $this->transformActionClass($actionName, $actionClass);
        }

        if (empty($unionMembers)) {
            $typescript = 'never';
        } else {
            $typescript = "\n  | ".implode("\n  | ", $unionMembers);
        }

        return TransformedType::create(
            $class,
            'Message',
            $typescript,
        );
    }

    private function transformActionClass(string $name, string $className): string
    {
        $class = new ReflectionClass($className);

        if (! $class->hasMethod('handle')) {
            return "{ name: \"{$name}\", input: any, output: any }";
        }

        $method = $class->getMethod('handle');

        $parameters = $method->getParameters();
        $inputParam = $parameters[0] ?? null;

        $inputType = $this->getInlinedType($inputParam?->getType());
        $returnType = $this->getInlinedType($method->getReturnType());

        return "{ name: \"{$name}\", input: {$inputType}, output: {$returnType} }";
    }

    private function getInlinedType(?ReflectionType $type): string
    {
        if (! $type) {
            return 'any';
        }

        // Handle PHP 8+ Union Types (e.g., string|int)
        if ($type instanceof ReflectionUnionType) {
            $types = array_map(fn ($t) => $this->getInlinedType($t), $type->getTypes());

            return implode(' | ', $types);
        }

        if (! $type instanceof ReflectionNamedType) {
            return 'any';
        }

        $name = $type->getName();

        // Map PHP types to TypeScript primitives
        $primitive = match ($name) {
            'string' => 'string',
            'int', 'float' => 'number',
            'bool' => 'boolean',
            // TODO: handle LaravelData DataCollection arrays
            'array' => 'any[]', // Basic fallback for arrays
            'void' => 'void',
            'mixed' => 'any',
            default => null,
        };

        if ($primitive) {
            return $primitive;
        }

        // If it's not a primitive, assume it's a DTO/Class and inline its properties
        if (class_exists($name)) {
            return $this->inlineClassProperties($name);
        }

        return 'any';
    }

    private function inlineClassProperties(string $className): string
    {
        $class = new ReflectionClass($className);
        // Grab only public properties. You can adjust this if you use getters/setters.
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        if (empty($properties)) {
            return '{}';
        }

        $propsTs = [];
        foreach ($properties as $property) {
            $propName = $property->getName();
            $propType = $this->getInlinedType($property->getType());

            // If the property is nullable in PHP, make it optional in TS
            $optional = $property->getType() && $property->getType()->allowsNull() ? '?' : '';

            $propsTs[] = "{$propName}{$optional}: {$propType}";
        }

        return '{ '.implode('; ', $propsTs).' }';
    }
}

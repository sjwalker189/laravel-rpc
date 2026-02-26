<?php

namespace App\Message;

use ReflectionClass;

trait IsRegistry
{
    public function getHandler(string $name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        $reflection = new ReflectionClass($this);
        if (! $reflection->hasProperty($name)) {
            return null;
        }

        $property = $reflection->getProperty($name);
        $type = $property->getType();

        if ($type && ! $type->isBuiltin()) {
            $className = $type->getName();
            $this->{$name} = app($className);

            return $this->{$name};
        }

        return null;
    }
}

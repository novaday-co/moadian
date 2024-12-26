<?php

namespace Novaday\Moadian\Concerns;

use ReflectionClass;
use ReflectionProperty;

trait Arrayable
{
    public function toArray(): array
    {
        $array = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $array[$property->getName()] = $property->isInitialized($this) ? $property->getValue($this) : null;
        }

        return $array;
    }
}

<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\Container\ContainerException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private array $entries = [];

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable|string $concrete)
    {
        $this->entries[$id] = $concrete;
    }

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];

            if (is_callable($entry)) {
                return $entry($this);
            }

            $id = $entry;
        }

        return $this->resolve($id);
    }

    public function resolve($id)
    {
        $reflectionClass = new ReflectionClass($id);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException('Class' . $id . ' is not instantiable.');
        }

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $id;
        }

        $parameters = $constructor->getParameters();
        if (!$parameters) {
            return new $id;
        }

        $dependencies = array_map(function (ReflectionParameter $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if (!$type) {
                throw new ContainerException('Parameter ' . $name . ' has no type hint!');
            }

            if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
                throw new ContainerException('Parameter ' . $name . ' is a union/intersection type!');
            }

            if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
                throw new ContainerException('Parameter ' . $name . ' is a builtin type!');
            }

            return $this->get($type->getName());
        }, $parameters);

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}

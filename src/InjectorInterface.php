<?php

namespace Hi;

interface InjectorInterface
{
    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public function construct(string $id): object;
}

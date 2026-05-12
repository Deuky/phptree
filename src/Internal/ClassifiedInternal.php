<?php

namespace PhpTree\Internal;

use Closure;
use Iterator;

class ClassifiedInternal implements Iterator
{
    protected ?int $currentIndex;
    protected array $keys;
    public readonly array $values;

    public function __construct(
        protected readonly array $array,
        protected readonly Closure $callback,
        protected readonly bool $sort
    ) {
        $classified = array_reduce(
            $array,
            function($carry, $item) use ($callback) {
                $carry[$callback($item)][] = $item;
                return $carry;
            },
            []
        );

        $this->keys = array_keys($classified);
        if ($sort) {
            asort($this->keys);
        }

        $this->values = array_values($classified);
        $this->currentIndex = key($this->keys);
    }

    public function current(): mixed
    {
        return $this->values[$this->currentIndex];
    }

    public function next(): void
    {
        next($this->keys);
        $this->currentIndex = key($this->keys);
    }

    public function key(): mixed
    {
        return current($this->keys);
    }

    public function rewind(): void
    {
        reset($this->keys);
        $this->currentIndex = key($this->keys);
    }

    public function valid(): bool
    {
        return is_int($this->currentIndex);
    }
}
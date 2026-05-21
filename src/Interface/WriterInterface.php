<?php

namespace PhpTree\Interface;

interface WriterInterface
{
    public function write(string ...$contents): void;
}
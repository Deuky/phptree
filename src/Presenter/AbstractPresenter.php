<?php

namespace PhpTree\Presenter;

use PhpTree\Interface\PresenterInterface;
use PhpTree\Interface\WriterInterface;

abstract class AbstractPresenter implements PresenterInterface
{
    public function __construct(
        public readonly WriterInterface $writer,
        public readonly string $maskPath = '' 
    ) {
    }

    protected function relativePath(string $absolutePath): string
    {
        $base = rtrim($this->maskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($absolutePath, $base)) {
            throw new \RuntimeException(sprintf(
                'File "%s" is outside the scanned directory "%s".',
                $absolutePath,
                $base
            ));
        }
        return substr($absolutePath, strlen($base));
    }
}
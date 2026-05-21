<?php

namespace PhpTree\Scanner;

use SplFileInfo;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Generator;

class DirectoryScanner
{
    public readonly array $excludes;

    public function __construct(
        array $excludes = []
    ){
        $this->excludes = $excludes;
    }

    /**
     * @return SplFileInfo[]
     */
    public function scan(string $directory): Generator
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                RecursiveDirectoryIterator::SKIP_DOTS,
            ),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        /** 
         * @var SplFileInfo $file
         */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            if ($this->isExcluded($file->getPathname(), $directory)) {
                continue;
            }

            yield $file; 
        }
    }

    private function isExcluded(string $filePath, string $baseDir): bool
    {
        if (!$this->excludes) {
            return false;
        }
        
        $relativePath = ltrim(explode($baseDir, $filePath)[1], ".".DIRECTORY_SEPARATOR);

        return array_all(
            $this->excludes,
            fn($exclude) => str_starts_with($relativePath, $exclude)
        );
    }
}
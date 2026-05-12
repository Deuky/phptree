<?php

namespace PhpTree\Interface;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
 
interface OutputInterface
{
    /**
     * @param NodeNormalizer[] $nodes
     */
    public function render(array $nodes, ?string $outputPath): void;
}

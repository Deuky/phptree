<?php

namespace PhpTree\Interface;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
 
interface PresenterInterface
{
    /**
     * @param NodeNormalizer ...$nodes
     */
    public function render(NodeNormalizer ...$nodes): void;
}

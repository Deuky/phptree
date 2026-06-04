<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpTree\Trait\Constant;

class EnumNodeNormalizer extends AbstractObjectNodeNormalizer
{
	use Constant;

	protected function initImplements(): array
	{
		return array_map(
            fn(Node\Name $n): string => $n->toString(),
            $this->node->implements,
        );
	}
}
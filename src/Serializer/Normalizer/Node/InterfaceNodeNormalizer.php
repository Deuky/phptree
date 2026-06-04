<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Trait\Constant;
use function implode;

class InterfaceNodeNormalizer extends AbstractObjectNodeNormalizer
{
	use Constant;
	
	protected function initExtends(): string
	{
        return implode(', ', $this->extendsList);
	}
}
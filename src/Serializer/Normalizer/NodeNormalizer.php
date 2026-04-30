<?php

namespace PhpTree\Serializer\Normalizer;

use PhpParser\Node as NodeInterface;
use PhpParser\Node\Stmt;
use ArrayAccess;
use Iterator;

class NodeNormalizer implements ArrayAccess, Iterator
{
	const array NODE_CLASS_MAPPER = [
		Stmt\Class_::class => Node\ClassNodeNormalizer::class,
		Stmt\Trait_::class => Node\TraitNodeNormalizer::class,
		Stmt\Interface_::class => Node\InterfaceNodeNormalizer::class,
		Stmt\Enum_::class => Node\EnumNodeNormalizer::class,
	];

	protected array $normalizer;
	public readonly Node\AbstractNodeNormalizer $nodeNormalizer;
	
	public function __construct(
		public readonly NodeInterface $node,
		...$args
	)
	{
        $normalizerClassName = $this::NODE_CLASS_MAPPER[$node::class];
        $this->nodeNormalizer = new $normalizerClassName(
        	$node, 
        	...$args
        );
	}

	protected function getExtend(null|array $a)
	{
		$this->extends = "ok";
	}

	public function offsetExists(mixed $offset): bool
	{
		die(__METHOD__);
	}

	public function offsetGet(mixed $offset): string
	{
		die(__METHOD__);
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		die(__METHOD__);
	}

	public function offsetUnset(mixed $offset): void
	{
		die(__METHOD__);
	}

	public function current(): string
	{
		die(__METHOD__);

		return current($this->normalizer);
	}

	public function next(): void
	{
		die(__METHOD__);

		next($this->normalizer);
	}

	public function key(): ?string
	{
		die(__METHOD__);

		return key($this->normalizer);
	}

	public function valid(): bool
	{
		die(__METHOD__);

		$key = $this->key();

		if ($key === null) {
			return false;
		}

		return key_exists(
			$this->key(),
			$this->normalizer
		);
	}

	public function rewind(): void
	{
		die(__METHOD__);
		$this->normalizer = $this->toArray();
	}

	public function toArray(): array
	{
		die(__METHOD__);
		return $this->nodeNormalizer->toArray();
	}

	public function __get(string $name): mixed
	{
		return $this->nodeNormalizer->{$name};
	}
}

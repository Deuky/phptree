<?php

namespace PhpTree\Writer;

use PhpTree\Interface\WriterInterface;

class NullWriter implements WriterInterface
{
	public function write(string ...$contents): void
	{
		return;
	}
}
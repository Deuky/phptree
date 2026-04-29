<?php

namespace PhpTree\Parser;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpTree\Parser\Data\RawClassData;
use RuntimeException;
use function file_get_contents;

final class PhpFileParser
{
    public function parse(string $filePath): ?RawClassData
    {
        $code = file_get_contents($filePath);

        if ($code === false) {
            throw new \RuntimeException(sprintf('Impossible de lire le fichier : %s', $filePath));
        }

        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        try {
            $ast = $parser->parse($code);
        } catch (Error $e) {
            throw new RuntimeException(sprintf(
                'Erreur de parsing dans %s : %s',
                $filePath,
                $e->getMessage(),
            ));
        }

        if ($ast === null) {
            return null;
        }

        $visitor = new ClassExtractorVisitor($filePath);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->getRawClassData();
    }
}
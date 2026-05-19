<?php

namespace PhpTree\Presenter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;
use PhpTree\Interface\PresenterInterface;
use PhpTree\Interface\WriterInterface;

class JsonPresenter implements PresenterInterface
{
    public function __construct(
        public readonly string $maskPath,
        public readonly WriterInterface $writer
    ) {}

    public function render(array $nodes): void
    {
        $classes = array_map(
            fn(NodeNormalizer $node): array => $this->serializeClass($node),
            $nodes,
        );

        $json = json_encode(['classes' => $classes], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $this->writer->write($json);
    }

    private function serializeClass(NodeNormalizer $node): array
    {
        return [
            'name'        => $node->name,
            'fqcn'        => $node->fqcn,
            'namespace'   => $node->namespace,
            'file'        => $this->relativePath($node->filePath),
            'type'        => $node->type,
            'abstract'    => $node->isAbstract,
            'final'       => $node->isFinal,
            'extends'     => $node->extends,
            'implements'  => $node->implements,
            'description' => $node->description,
            'methods'     => array_map(
                fn(MethodNodeNormalizer $method): array => $this->serializeMethod($method),
                $node->methods,
            ),
        ];
    }

    private function serializeMethod(MethodNodeNormalizer $method): array
    {
        return [
            'name'        => $method->name,
            'visibility'  => $method->visibility,
            'static'      => $method->isStatic,
            'abstract'    => $method->isAbstract,
            'return_type' => (string) $method->returnType ?: null,
            'description' => $method->description,
            'throws'      => $method->throws,
            'parameters'  => array_map(
                fn(ParameterNodeNormalizer $param): array => $this->serializeParameter($param),
                $method->parameters,
            ),
        ];
    }

    private function serializeParameter(ParameterNodeNormalizer $param): array
    {
        return [
            'name'        => $param->name,
            'type'        => (string) $param->type ?: null,
            'nullable'    => $param->isNullable,
            'has_default' => $param->hasDefault,
            'default'     => $param->defaultValue,
        ];
    }

    private function relativePath(string $absolutePath): string
    {
        $base = rtrim($this->maskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return str_starts_with($absolutePath, $base)
            ? substr($absolutePath, strlen($base))
            : $absolutePath; // fallback si hors du répertoire scanné
    }
}
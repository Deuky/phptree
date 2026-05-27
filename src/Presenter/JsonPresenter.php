<?php

namespace PhpTree\Presenter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\PropertyNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ConstantNodeNormalizer;
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
        ] + (
            
            match ($node->type) {
                'class', 'trait' => [
                    'constants'   => array_map(
                        fn(ConstantNodeNormalizer $const): array => $this->serializeConstant($const),
                        $node->constants ?? [],
                    ),
                    'properties'  => array_map(
                        fn(PropertyNodeNormalizer|ParameterNodeNormalizer $prop): array => $this->serializeProperty($prop),
                        $node->properties ?? [],
                    ),
                ],
                'enum', 'interface' => [
                    'constants'   => array_map(
                        fn(ConstantNodeNormalizer $const): array => $this->serializeConstant($const),
                        $node->constants ?? [],
                    ),
                ],
                default => []
            }
        );
    }

    private function serializeConstant(ConstantNodeNormalizer $const): array
    {
        return [
            'name'       => $const->name,
            'value'      => $const->value,
            'visibility' => $const->visibility,
            'type'       => (string) $const->type ?: null,
        ];
    }

    private function serializeProperty(PropertyNodeNormalizer|ParameterNodeNormalizer $prop): array
    {
        return [
            'name'       => $prop->name,
            'type'       => (string) $prop->type ?: null,
            'visibility' => $prop->visibility,
            'static'     => $prop->static,
            'readonly'   => $prop->readonly,
            'default'    => $prop->defaultValue,
            'description'=> $prop->description,
        ];
    }

    private function serializeMethod(MethodNodeNormalizer $method): array
    {
        return [
            'name'        => $method->name,
            'visibility'  => $method->visibility,
            'static'      => $method->isStatic,
            'abstract'    => $method->isAbstract,
            'return_type' => $method->returnType ?: null,
            'description' => $method->description,
            'throws'      => $method->throws,
            'parameters'  => array_map(
                fn(ParameterNodeNormalizer $param): array => $this->serializeParameter($param, $method->isConstructor),
                $method->parameters,
            ),
        ];
    }

    private function serializeParameter(ParameterNodeNormalizer $param, bool $constructor): array
    {
        return [
            'name'        => $param->name,
            'type'        => (string) $param->type ?: null,
            'nullable'    => $param->isNullable,
            'has_default' => $param->hasDefault,
            'default'     => $param->defaultValue,
        ] + (
            $constructor ? [
                "readonly" => $param->readonly
            ] : []
        );
    }

    private function relativePath(string $absolutePath): string
    {
        $base = rtrim($this->maskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return str_starts_with($absolutePath, $base)
            ? substr($absolutePath, strlen($base))
            : $absolutePath; // fallback si hors du répertoire scanné
    }
}
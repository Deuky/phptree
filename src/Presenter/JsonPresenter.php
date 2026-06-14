<?php

namespace PhpTree\Presenter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\PropertyNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ConstantNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\EnumCaseNodeNormalizer;
use function array_map, json_encode;

class JsonPresenter extends AbstractPresenter
{
    public function render(NodeNormalizer ...$nodes): void
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
            'uses'        => [
                'class' => $node->useClasses,
                'function' => $node->useFunctions
            ],
            'file'        => $this->relativePath($node->filePath),
            'type'        => (string) $node->type,
            'abstract'    => $node->abstract,
            'final'       => $node->final,
            'extends'     => $node->extends,
            'implements'  => $node->implements,
            'description' => $node->description,
            'methods'     => array_map(
                fn(MethodNodeNormalizer $method): array => $this->serializeMethod($method),
                $node->methods,
            ),
        ] + (
            match ((string) $node->type) {
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
                'enum' => [
                    'cases'       => array_map(
                        fn(EnumCaseNodeNormalizer $case): array => $this->serializeEnumCase($case),
                        $node->cases ?? [],
                    ),
                    'constants'   => array_map(
                        fn(ConstantNodeNormalizer $const): array => $this->serializeConstant($const),
                        $node->constants ?? [],
                    ),
                ],
                'interface' => [
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
            'default'    => $prop->default,
            'description'=> $prop->description,
        ];
    }

    private function serializeMethod(MethodNodeNormalizer $method): array
    {
        return [
            'name'        => $method->name,
            'visibility'  => $method->visibility,
            'static'      => $method->static,
            'abstract'    => $method->abstract,
            'return_type' => ((string) $method->type) ?: null,
            'description' => $method->description,
            'throws'      => $method->throws,
            'parameters'  => array_map(
                fn(ParameterNodeNormalizer $param): array => $this->serializeParameter($param, $method->constructor),
                $method->parameters,
            ),
        ];
    }

    private function serializeParameter(ParameterNodeNormalizer $param, bool $constructor): array
    {
        return [
            'name'        => $param->name,
            'type'        => ((string) $param->type) ?: null ,
            'variadic'    => $param->variadic,
            'default'     => $param->default,
        ] + (
            $constructor ? [
                "readonly" => $param->readonly
            ] : []
        );
    }

    private function serializeEnumCase(EnumCaseNodeNormalizer $case): array
    {
        return [
            'description' => $case->description,
            'name'  => $case->name,
            'value' => $case->value,
        ];
    }
}
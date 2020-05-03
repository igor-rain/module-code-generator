<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\GraphQl;

use GraphQL\Language\AST;
use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\GraphQlSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;

class SchemaGraphQlsGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generateSchema(string $fileName, ModelContext $context): void
    {
        /** @var GraphQlSource $source */
        $source = $this->sourceFactory->create($fileName, 'graphql');
        if ($source->exists()) {
            $source->load();
        }

        $objectName = $context->getVariableName();
        $objectType = ucfirst($objectName);

        $definitions = $this->convertToArray($source->getDocumentNode()->definitions);

        $queryType = $this->findDefinitionByName($definitions, 'Query');
        if (!$queryType) {
            $queryType = $this->createDefinitionWithName('Query');
            $definitions[] = $queryType;
        }

        $modelType = $this->findDefinitionByName($definitions, $objectType);
        if (!$modelType) {
            $modelType = $this->createDefinitionWithName($objectType);
            $definitions[] = $modelType;
        }

        $queryFields = $this->convertToArray($queryType->fields);
        $queryField = $this->findFieldByName($queryFields, $objectName);
        if (!$queryField) {
            $queryField = $this->createField($objectName, $objectType, [
                $this->creatFieldArgument('id', $context->getPrimaryKey()->getGraphQlType(), [
                    $this->createDirective('doc', 'description', ucfirst($context->getClassDescription()) . ' id')
                ])
            ], [
                $this->createDirective('resolver', 'class', $context->getGraphQlModelResolver()->getName()),
                $this->createDirective('doc', 'description', 'The ' . $context->getClassDescription() . ' query returns information about a ' . $context->getClassDescription())
            ]);
            $queryFields[] = $queryField;
        }
        $queryType->fields = AST\NodeList::create($queryFields);

        $modelFields = $this->convertToArray($modelType->fields);
        foreach ($context->getFields() as $field) {
            if ($field->isPrimary()) {
                $fieldName = 'id';
                $fieldDescription = ucfirst($context->getClassDescription()) . ' id';
            } else {
                $fieldName = $field->getName();
                $fieldDescription = ucfirst($context->getClassDescription()) . ' ' . $field->getDescription();
            }

            if (!$this->findFieldByName($modelFields, $fieldName)) {
                $modelFields[] = $this->createField($fieldName, $field->getGraphQlType(), [], [
                    $this->createDirective('doc', 'description', $fieldDescription)
                ]);
            }
        }
        $modelType->fields = AST\NodeList::create($modelFields);
        $modelDirectives = $this->convertToArray($modelType->directives);
        if (!$modelDirectives) {
            $modelDirectives[] = $this->createDirective('doc', 'description', ucfirst($context->getClassDescription()) . ' information');
        }
        $modelType->directives = AST\NodeList::create($modelDirectives);

        $source->getDocumentNode()->definitions = AST\NodeList::create($definitions);
        $source->save();
    }

    private function convertToArray($data): array
    {
        if ($data) {
            if (is_array($data)) {
                return $data;
            }
            return iterator_to_array($data);
        }
        return [];
    }

    private function findDefinitionByName(array $definitions, string $type): ?AST\ObjectTypeDefinitionNode
    {
        foreach ($definitions as $definition) {
            if ($definition instanceof AST\ObjectTypeDefinitionNode && $definition->name instanceof AST\NameNode) {
                if ($definition->name->value === $type) {
                    return $definition;
                }
            }
        }
        return null;
    }

    private function createDefinitionWithName(string $type): AST\ObjectTypeDefinitionNode
    {
        return new AST\ObjectTypeDefinitionNode([
            'name' => $this->createNameNode($type),
        ]);
    }

    private function findFieldByName(array $fields, string $name): ?AST\FieldDefinitionNode
    {
        foreach ($fields as $field) {
            if ($field instanceof AST\FieldDefinitionNode && $field->name instanceof AST\NameNode) {
                if ($field->name->value === $name) {
                    return $field;
                }
            }
        }
        return null;
    }

    private function createNameNode($name): AST\NameNode
    {
        return new AST\NameNode([
            'value' => $name
        ]);
    }

    private function createTypeNode($type): AST\NamedTypeNode
    {
        return new AST\NamedTypeNode([
            'name' => $this->createNameNode($type)
        ]);
    }

    private function createField(string $name, string $type, array $arguments, array $directives): AST\FieldDefinitionNode
    {
        return new AST\FieldDefinitionNode([
            'name' => $this->createNameNode($name),
            'type' => $this->createTypeNode($type),
            'arguments' => AST\NodeList::create($arguments),
            'directives' => AST\NodeList::create($directives)
        ]);
    }

    private function creatFieldArgument(string $name, string $type, array $directives): AST\InputValueDefinitionNode
    {
        return new AST\InputValueDefinitionNode([
            'name' => $this->createNameNode($name),
            'type' => $this->createTypeNode($type),
            'directives' => AST\NodeList::create($directives)
        ]);
    }

    private function createDirective($type, $name, $content): AST\DirectiveNode
    {
        return new AST\DirectiveNode([
            'name' => $this->createNameNode($type),
            'arguments' => AST\NodeList::create([
                new AST\ArgumentNode([
                    'name' => $this->createNameNode($name),
                    'value' => new AST\StringValueNode([
                        'value' => $content
                    ])
                ])
            ])
        ]);
    }
}

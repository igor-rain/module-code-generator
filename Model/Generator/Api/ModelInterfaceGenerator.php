<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use PhpParser\BuilderFactory;
use PhpParser\Node;

class ModelInterfaceGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate(string $fileName, ModelContext $context): void
    {
        /** @var PhpSource $source */
        $source = $this->sourceFactory->create($fileName, 'php');
        $source->setStmts($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context): array
    {
        $factory = new BuilderFactory();
        $interface = $factory->interface($context->getModelInterface()->getShortName());
        $interface->setDocComment('/**
            * @api
            */');

        foreach ($context->getFields() as $field) {
            if (!$field->isPrimary()) {
                $interface->addStmt(new Node\Stmt\ClassConst([
                    new Node\Const_($field->getConstantName(), new Node\Scalar\String_($field->getName())),
                ], Node\Stmt\Class_::MODIFIER_PUBLIC));
            }
        }

        foreach ($context->getFields() as $field) {
            if ($field->isPrimary()) {
                $getMethod = $factory->method('getId')
                    ->makePublic()
                    ->setReturnType(new Node\NullableType($field->getPhpType()))
                    ->setDocComment('/**
                    * ' . ucfirst($context->getClassDescription()) . ' id
                    *
                    * @return ' . $field->getPhpType() . '|null
                    */');

                $setMethod = $factory->method('setId')
                    ->makePublic()
                    ->addParam($factory->param('id'))
                    ->setDocComment('/**
                    * Set ' . $context->getClassDescription() . ' id
                    *
                    * @param ' . $field->getPhpType() . '|null $id
                    * @return $this
                    */');

                $interface->addStmt($getMethod);
                $interface->addStmt($setMethod);
            } else {
                $getMethod = $factory->method($field->getMethodName('get'))
                    ->makePublic()
                    ->setReturnType(new Node\NullableType($field->getPhpType()))
                    ->setDocComment('/**
                    * ' . ucfirst($context->getClassDescription()) . ' ' . $field->getDescription() . '
                    *
                    * @return ' . $field->getPhpType() . '|null
                    */');

                $setMethod = $factory->method($field->getMethodName('set'))
                    ->makePublic()
                    ->setReturnType(new Node\Name('self'))
                    ->addParam($factory->param($field->getVariableName())->setType(new Node\NullableType($field->getPhpType())))
                    ->setDocComment('/**
                    * Set ' . $context->getClassDescription() . ' ' . $field->getDescription() . '
                    *
                    * @param ' . $field->getPhpType() . '|null $' . $field->getVariableName() . '
                    * @return $this
                    */');

                $interface->addStmt($getMethod);
                $interface->addStmt($setMethod);
            }
        }

        $node = $factory->namespace($context->getModelInterface()->getNamespace())
            ->addStmt($interface)
            ->getNode();

        return [$node];
    }
}

<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
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
            if (!$field->getIsPrimary()) {
                $interface->addStmt(new Node\Stmt\ClassConst([
                    new Node\Const_($field->getConstantName(), new Node\Scalar\String_($field->getName())),
                ], Node\Stmt\Class_::MODIFIER_PUBLIC));
            }
        }

        $methodFields = array_merge(
            [new ModelFieldContext('id')],
            $context->getFields()
        );
        foreach ($methodFields as $field) {
            if (!$field->getIsPrimary()) {
                $getMethod = $factory->method($field->getMethodName('get'))
                    ->makePublic()
                    ->setDocComment('/**
                    * ' . ucfirst($context->getClassDescription()) . ' ' . $field->getDescription() . '
                    *
                    * @return string|null
                    */');

                $setMethod = $factory->method($field->getMethodName('set'))
                    ->makePublic()
                    ->addParam($factory->param($field->getVariableName()))
                    ->setDocComment('/**
                    * Set ' . $context->getClassDescription() . ' ' . $field->getDescription() . '
                    *
                    * @param string $' . $field->getVariableName() . '
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

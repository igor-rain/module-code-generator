<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use Magento\Framework\Model\AbstractModel;
use PhpParser\BuilderFactory;
use PhpParser\Node;

class ModelClassGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate($fileName, ModelContext $context)
    {
        /** @var PhpSource $source */
        $source = $this->sourceFactory->create($fileName, 'php');
        $source->setStmts($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context)
    {
        $factory = new BuilderFactory();
        $class = $factory->class($context->getModel()->getShortName());
        $class->extend('AbstractModel');
        $class->implement($context->getModelInterface()->getShortName());

        $eventPrefix = $factory->property('_eventPrefix');
        $eventPrefix->makeProtected();
        $eventPrefix->setDocComment('/**
            * Event prefix
            *
            * @var string
            */');
        $eventPrefix->setDefault($context->getEventPrefixName());

        $eventObject = $factory->property('_eventObject');
        $eventObject->makeProtected();
        $eventObject->setDocComment('/**
            * Event object name
            *
            * @var string
            */');
        $eventObject->setDefault($context->getEventObjectName());

        $class->addStmt($eventPrefix);
        $class->addStmt($eventObject);

        $construct = $factory->method('_construct')
            ->makeProtected()
            ->addStmt(
                new Node\Expr\MethodCall(new Node\Expr\Variable('this'), '_init', [
                    new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name($context->getResourceModel()->getShortName() . 'Resource'), 'class')),
                ])
            );

        $class->addStmt($construct);

        foreach ($context->getFields() as $field) {
            if (!$field->getIsPrimary()) {
                $getMethod = $factory->method($field->getMethodName('get'))
                    ->makePublic()
                    ->addStmt(
                        new Node\Stmt\Return_(new Node\Expr\MethodCall(new Node\Expr\Variable('this'), 'getData', [
                            new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name('self'), $field->getConstantName())),
                        ]))
                    );

                $setMethod = $factory->method($field->getMethodName('set'))
                    ->makePublic()
                    ->addParam($factory->param($field->getVariableName()))
                    ->addStmt(
                        new Node\Stmt\Return_(new Node\Expr\MethodCall(new Node\Expr\Variable('this'), 'setData', [
                            new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name('self'), $field->getConstantName())),
                            new Node\Arg(new Node\Expr\Variable($field->getVariableName())),
                        ]))
                    );

                $class->addStmt($getMethod);
                $class->addStmt($setMethod);
            }
        }

        $node = $factory->namespace($context->getModel()->getNamespace())
            ->addStmt($factory->use(AbstractModel::class))
            ->addStmt($factory->use($context->getResourceModel()->getName())->as($context->getResourceModel()->getShortName() . 'Resource'))
            ->addStmt($factory->use($context->getModelInterface()->getName()))
            ->addStmt($class)
            ->getNode();

        return [$node];
    }
}

<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PhpParser\BuilderFactory;
use PhpParser\Node;

class CollectionClassGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate($fileName, ModelContext $context): void
    {
        /** @var PhpSource $source */
        $source = $this->sourceFactory->create($fileName, 'php');
        $source->setStmts($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context): array
    {
        $factory = new BuilderFactory();
        $class = $factory->class($context->getCollection()->getShortName());
        $class->extend('AbstractCollection');

        $eventPrefix = $factory->property('_eventPrefix');
        $eventPrefix->makeProtected();
        $eventPrefix->setDocComment('/**
            * Event prefix
            *
            * @var string
            */');
        $eventPrefix->setDefault($context->getEventPrefixName() . '_collection');

        $eventObject = $factory->property('_eventObject');
        $eventObject->makeProtected();
        $eventObject->setDocComment('/**
            * Event object name
            *
            * @var string
            */');
        $eventObject->setDefault($context->getEventObjectName() . '_collection');

        $construct = $factory->method('_construct')
            ->makeProtected()
            ->addStmt(
                new Node\Expr\MethodCall(new Node\Expr\Variable('this'), '_init', [
                    new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name($context->getModel()->getShortName()), 'class')),
                    new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name($context->getResourceModel()->getShortName() . 'Resource'), 'class')),
                ])
            );

        $class->addStmt($eventPrefix);
        $class->addStmt($eventObject);
        $class->addStmt($construct);

        $node = $factory->namespace($context->getCollection()->getNamespace())
            ->addStmt($factory->use(AbstractCollection::class))
            ->addStmt($factory->use($context->getModel()->getName()))
            ->addStmt($factory->use($context->getResourceModel()->getName())->as($context->getResourceModel()->getShortName() . 'Resource'))
            ->addStmt($class)
            ->getNode();

        return [$node];
    }
}

<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use PhpParser\BuilderFactory;
use PhpParser\Comment;
use PhpParser\Node;

class ResourceModelClassGenerator
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
        $shortModelInterfaceName = $context->getModelInterface()->getShortName();
        $shortResourceModelClassName = $context->getResourceModel()->getShortName();

        $factory = new BuilderFactory();
        $class = $factory->class($context->getResourceModel()->getShortName());
        $class->extend('AbstractDb');
        $class->setDocComment('/**
            * @method ' . $shortResourceModelClassName . ' load(' . $shortModelInterfaceName . ' $object, $value, $field=null)
            * @method ' . $shortResourceModelClassName . ' delete(' . $shortModelInterfaceName . ' $object)
            * @method ' . $shortResourceModelClassName . ' save(' . $shortModelInterfaceName . ' $object)
            */');

        $predefinedId = new Node\Stmt\TraitUse([new Node\Name('PredefinedId')]);
        $predefinedId->setDocComment(new Comment\Doc('/**
            * Provides possibility of saving entity with predefined/pre-generated id
            */'));

        $class->addStmt($predefinedId);

        $construct = $factory->method('_construct')
            ->makeProtected()
            ->addStmt(
                new Node\Expr\MethodCall(new Node\Expr\Variable('this'), '_init', [
                    new Node\Arg(new Node\Scalar\String_($context->getTableName())),
                    new Node\Arg(new Node\Scalar\String_($context->getPrimaryKey()->getName())),
                ])
            );

        $class->addStmt($construct);

        $node = $factory->namespace($context->getResourceModel()->getNamespace())
            ->addStmt($factory->use(AbstractDb::class))
            ->addStmt($factory->use('Magento\Framework\Model\ResourceModel\PredefinedId'))
            ->addStmt($factory->use($context->getModelInterface()->getName()))
            ->addStmt($class)
            ->getNode();

        return [$node];
    }
}

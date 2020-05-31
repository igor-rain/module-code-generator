<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource\PrettyPrinter
 */
class PhpSourceTest extends TestCase
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var PhpSource
     */
    private $source;

    public function setUp(): void
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'test');
        $this->source = new PhpSource($this->fileName);
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testLoadSave(): void
    {
        file_put_contents($this->fileName, $this->getSamplePhp());
        $this->source->load();
        $this->source->save();
        $this->assertEquals($this->getSamplePhp(), file_get_contents($this->fileName));
    }

    public function testGetStmts(): void
    {
        file_put_contents($this->fileName, $this->getSamplePhp());
        $this->source->load();
        $this->assertCount(1, $this->source->getStmts());
    }

    public function testSetStmts(): void
    {
        $factory = new BuilderFactory();
        $node = $factory->namespace('Name\Space')
            ->addStmt($factory->use('Some\Other\Thingy')->as('SomeClass'))
            ->addStmt($factory->useFunction('strlen'))
            ->addStmt($factory->useConst('PHP_VERSION'))
            ->addStmt(
                $factory->class('SomeOtherClass')
                ->extend('SomeClass')
                ->implement('A\Few', '\Interfaces')
                ->makeAbstract()

                ->addStmt($factory->useTrait('FirstTrait'))

                ->addStmt($factory->useTrait('SecondTrait', 'ThirdTrait')
                    ->and('AnotherTrait')
                    ->with($factory->traitUseAdaptation('foo')->as('bar'))
                    ->with($factory->traitUseAdaptation('AnotherTrait', 'baz')->as('test'))
                    ->with($factory->traitUseAdaptation('AnotherTrait', 'func')->insteadof('SecondTrait')))

                ->addStmt(
                    $factory->method('someMethod')
                    ->makePublic()
                    ->makeAbstract() // ->makeFinal()
                    ->setReturnType('bool') // ->makeReturnByRef()
                    ->addParam($factory->param('someParam')->setType('SomeClass'))
                    ->setDocComment('/**
                              * This method does something.
                              *
                              * @param SomeClass And takes a parameter
                              */')
                )

                ->addStmt(
                    $factory->method('anotherMethod')
                    ->makeProtected()
                    ->addParam($factory->param('someParam')->setDefault('test'))
                    ->addStmt(new Node\Expr\Print_(new Node\Expr\Variable('someParam')))
                )
                ->addStmt($factory->property('someProperty')->makeProtected())
                ->addStmt($factory->property('anotherProperty')->makePrivate()->setDefault([1, 2, 3]))
            )

            ->getNode();

        $this->source->setStmts([$node]);
        $this->source->save();
        $this->assertEquals('<?php

namespace Name\Space;

use Some\Other\Thingy as SomeClass;
use function strlen;
use const PHP_VERSION;
abstract class SomeOtherClass extends SomeClass implements A\Few, \Interfaces
{
    use FirstTrait;

    use SecondTrait, ThirdTrait, AnotherTrait {

        foo as bar;

        AnotherTrait::baz as test;

        AnotherTrait::func insteadof SecondTrait;
    }

    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);

    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    public abstract function someMethod(SomeClass $someParam): bool;

    protected function anotherMethod($someParam = \'test\')
    {
        print $someParam;
    }
}
', file_get_contents($this->fileName));
    }

    protected function getSamplePhp(): string
    {
        return '<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Api\Data;

/**
 * @api
 * @since 100.0.2
 */
interface ProductInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SKU = \'sku\';

    const NAME = \'name\';

    const PRICE = \'price\';

    const WEIGHT = \'weight\';

    const STATUS = \'status\';

    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set product id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);
}
';
    }
}

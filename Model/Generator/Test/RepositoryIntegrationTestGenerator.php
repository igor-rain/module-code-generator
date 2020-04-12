<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class RepositoryIntegrationTestGenerator
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
        /** @var TextSource $source */
        $source = $this->sourceFactory->create($fileName, 'text');
        $source->setContent($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context): string
    {
        return strtr($this->getTemplate(), [
            '{namespace}' => $context->getRepository()->getIntegrationTest()->getNamespace(),
            '{modelInterface}' => $context->getModelInterface()->getName(),
            '{shortModelInterface}' => $context->getModelInterface()->getShortName(),
            '{modelFactory}' => lcfirst($context->getModel()->getShortName() . 'Factory'),
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepository}' => $context->getRepository()->getShortName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
            '{description}' => $context->getClassDescription(),
            '{descriptionCapital}' => ucfirst($context->getClassDescription()),
            '{modelDataFixture}' => $context->getFixtureRelativePath('Integration', $context->getEventObjectName()),
            '{primaryKey}' => $context->getPrimaryKey()->getName(),
        ]);
    }

    protected function getTemplate(): string
    {
        return '<?php

namespace {namespace};

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use {modelInterface}Factory;
use {repositoryInterface};

class {shortRepository}Test extends TestCase
{
    public const EXISTING_ID = 333;
    public const NEW_ID = 444;
    public const MISSING_ID = 555;

    /**
     * @var {shortRepositoryInterface}
     */
    private $repository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var {shortModelInterface}Factory
     */
    private ${modelFactory};

    public function setUp()
    {
        $this->repository = Bootstrap::getObjectManager()->create({shortRepositoryInterface}::class);
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
        $this->{modelFactory} = Bootstrap::getObjectManager()->get({shortModelInterface}Factory::class);
    }

    public function testSave(): void
    {
        ${variable} = $this->{modelFactory}->create();
        ${variable}->setId(self::NEW_ID);

        $this->repository->save(${variable});

        ${variable}Loaded = $this->repository->getById(self::NEW_ID);
        $this->assertEquals(self::NEW_ID, ${variable}Loaded->getId());
        $this->repository->delete(${variable}Loaded);
    }

    /**
     * @magentoDataFixture {modelDataFixture}
     */
    public function testGetById(): void
    {
        ${variable} = $this->repository->getById(self::EXISTING_ID);
        $this->assertEquals(self::EXISTING_ID, ${variable}->getId());
    }

    /**
     * @expectedExceptionMessage {descriptionCapital} with id "555" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdWhenDoesNotExists(): void
    {
        $this->repository->getById(self::MISSING_ID);
    }

    /**
     * @magentoDataFixture {modelDataFixture}
     * @expectedExceptionMessage {descriptionCapital} with id "333" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDelete(): void
    {
        ${variable} = $this->repository->getById(self::EXISTING_ID);
        $this->repository->delete(${variable});
        $this->repository->getById(self::EXISTING_ID);
    }

    /**
     * @magentoDataFixture {modelDataFixture}
     * @expectedExceptionMessage {descriptionCapital} with id "333" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteById(): void
    {
        $this->repository->deleteById(self::EXISTING_ID);
        $this->repository->getById(self::EXISTING_ID);
    }

    /**
     * @magentoDataFixture {modelDataFixture}
     */
    public function testGetList(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\'{primaryKey}\', self::EXISTING_ID, \'eq\')
            ->create();
        $list = $this->repository->getList($searchCriteria);
        $count = $list->getTotalCount();
        $items = $list->getItems();

        $this->assertSame(1, $count);
        $this->assertCount(1, $items);

        $firstItem = reset($items);
        $this->assertEquals(self::EXISTING_ID, $firstItem->getId());
    }
}
';
    }
}

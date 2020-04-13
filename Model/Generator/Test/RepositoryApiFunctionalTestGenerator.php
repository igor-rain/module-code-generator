<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class RepositoryApiFunctionalTestGenerator
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
            '{namespace}' => $context->getRepository()->getApiFunctionalTest()->getNamespace(),
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepository}' => $context->getRepository()->getShortName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
            '{modelDataFixture}' => $context->getFixtureRelativePath('Integration', $context->getEventObjectName()),
            '{primaryKey}' => $context->getPrimaryKey()->getName(),
            '{serviceName}' => $context->getRepositoryInterface()->getMagentoServiceName()
        ]);
    }

    protected function getTemplate(): string
    {
        return '<?php

namespace {namespace};

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use {repositoryInterface};

class {shortRepository}Test extends WebapiAbstract
{
    public const SERVICE_NAME = \'{serviceName}\';
    public const SERVICE_VERSION = \'V1\';
    public const RESOURCE_PATH = \'/V1/{variable}\';

    public const EXISTING_ID = 333;
    public const NEW_ID = 444;
    public const MISSING_ID = 555;

    /**
     * @var {shortRepositoryInterface}
     */
    private $repository;
    /**
     * @var mixed
     */
    private $searchCriteriaBuilder;

    public function setUp()
    {
        $this->repository = Bootstrap::getObjectManager()->create({shortRepositoryInterface}::class);
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
    }

    /**
     * @magentoApiDataFixture {modelDataFixture}
     */
    public function testGet(): void
    {
        $serviceInfo = [
            \'rest\' => [
                \'resourcePath\' => self::RESOURCE_PATH . \'/\' . self::EXISTING_ID,
                \'httpMethod\' => Request::HTTP_METHOD_GET,
            ],
            \'soap\' => [
                \'service\' => self::SERVICE_NAME,
                \'serviceVersion\' => self::SERVICE_VERSION,
                \'operation\' => self::SERVICE_NAME . \'GetById\',
            ],
        ];

        ${variable} = $this->_webApiCall($serviceInfo, [\'{variable}Id\' => self::EXISTING_ID]);
        $this->assertEquals(self::EXISTING_ID, ${variable}[\'id\']);
    }

    /**
     * @magentoApiDataFixture {modelDataFixture}
     * @expectedException \Exception
     */
    public function testGetWhenDoesNotExists(): void
    {
        $serviceInfo = [
            \'rest\' => [
                \'resourcePath\' => self::RESOURCE_PATH . \'/\' . self::MISSING_ID,
                \'httpMethod\' => Request::HTTP_METHOD_GET,
            ],
            \'soap\' => [
                \'service\' => self::SERVICE_NAME,
                \'serviceVersion\' => self::SERVICE_VERSION,
                \'operation\' => self::SERVICE_NAME . \'GetById\',
            ],
        ];

        $this->_webApiCall($serviceInfo, [\'{variable}Id\' => self::MISSING_ID]);
    }

    public function testSave(): void
    {
        try {
            $this->repository->deleteById(self::NEW_ID);
        } catch (NoSuchEntityException $e) {
        }

        $serviceInfo = [
            \'rest\' => [
                \'resourcePath\' => self::RESOURCE_PATH,
                \'httpMethod\' => Request::HTTP_METHOD_POST,
            ],
            \'soap\' => [
                \'service\' => self::SERVICE_NAME,
                \'serviceVersion\' => self::SERVICE_VERSION,
                \'operation\' => self::SERVICE_NAME . \'Save\',
            ],
        ];

        ${variable} = $this->_webApiCall($serviceInfo, [
            \'{variable}\' => [
                \'id\' => self::NEW_ID
            ],
        ]);

        $this->assertEquals(self::NEW_ID, ${variable}[\'id\']);
    }

    /**
     * @magentoApiDataFixture {modelDataFixture}
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDelete(): void
    {
        $serviceInfo = [
            \'rest\' => [
                \'resourcePath\' => self::RESOURCE_PATH . \'/\' . self::EXISTING_ID,
                \'httpMethod\' => Request::HTTP_METHOD_DELETE,
            ],
            \'soap\' => [
                \'service\' => self::SERVICE_NAME,
                \'serviceVersion\' => self::SERVICE_VERSION,
                \'operation\' => self::SERVICE_NAME . \'DeleteById\',
            ],
        ];

        $this->_webApiCall($serviceInfo, [\'{variable}Id\' => self::EXISTING_ID]);
        $this->repository->getById(self::EXISTING_ID);
    }

    /**
     * @magentoApiDataFixture {modelDataFixture}
     */
    public function testList(): void
    {
        $searchData = $this->searchCriteriaBuilder
            ->addFilter(\'{primaryKey}\', self::EXISTING_ID, \'eq\')
            ->create()
            ->__toArray();

        $requestData = [\'searchCriteria\' => $searchData];
        $serviceInfo = [
            \'rest\' => [
                \'resourcePath\' => self::RESOURCE_PATH . \'/list\' . \'?\' . http_build_query($requestData),
                \'httpMethod\' => Request::HTTP_METHOD_GET,
            ],
            \'soap\' => [
                \'service\' => self::SERVICE_NAME,
                \'serviceVersion\' => self::SERVICE_VERSION,
                \'operation\' => self::SERVICE_NAME . \'GetList\',
            ],
        ];

        $searchResult = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResult[\'total_count\']);
        $this->assertCount(1, $searchResult[\'items\']);
        $this->assertEquals(self::EXISTING_ID, $searchResult[\'items\'][0][\'id\']);
    }
}
';
    }
}

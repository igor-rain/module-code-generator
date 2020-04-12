<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Generator\Test\RepositoryApiFunctionalTestGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractTextSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class RepositoryApiFunctionalTestGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new RepositoryApiFunctionalTestGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Test\Api\Model\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface;

class ItemRepositoryTest extends WebapiAbstract
{
    public const SERVICE_NAME = \'vendor1Module1ApiMenuItemRepositoryV1\';
    public const SERVICE_VERSION = \'V1\';
    public const RESOURCE_PATH = \'/V1/menuItem\';

    public const EXISTING_ID = 333;
    public const NEW_ID = 444;
    public const MISSING_ID = 555;

    /**
     * @var ItemRepositoryInterface
     */
    private $repository;
    /**
     * @var mixed
     */
    private $searchCriteriaBuilder;

    public function setUp()
    {
        $this->repository = Bootstrap::getObjectManager()->create(ItemRepositoryInterface::class);
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     */
    public function testGet()
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

        $menuItem = $this->_webApiCall($serviceInfo, [\'menuItemId\' => self::EXISTING_ID]);
        $this->assertEquals($menuItem[\'id\'], self::EXISTING_ID);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     * @expectedException \Exception
     */
    public function testGetWhenDoesNotExists()
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

        $this->_webApiCall($serviceInfo, [\'menuItemId\' => self::MISSING_ID]);
    }

    public function testSave()
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

        $menuItem = $this->_webApiCall($serviceInfo, [
            \'menuItem\' => [
                \'id\' => self::NEW_ID
            ],
        ]);

        $this->assertEquals($menuItem[\'id\'], self::NEW_ID);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDelete()
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

        $this->_webApiCall($serviceInfo, [\'menuItemId\' => self::EXISTING_ID]);
        $this->repository->getById(self::EXISTING_ID);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     */
    public function testList()
    {
        $searchData = $this->searchCriteriaBuilder
            ->addFilter(\'entity_id\', self::EXISTING_ID, \'eq\')
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

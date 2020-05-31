<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Make;

use IgorRain\CodeGenerator\Model\Make\Model;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Make\Model
 */
class ModelTest extends TestCase
{
    /**
     * @var MockObject[]
     */
    private $parameters = [];
    /**
     * @var Model
     */
    private $makeModel;
    /**
     * @var array
     */
    private $checkedGenerators = [];

    public function setUp(): void
    {
        $class = new \ReflectionClass(Model::class);
        $constructor = $class->getConstructor();
        foreach ($constructor->getParameters() as $parameter) {
            $this->parameters[$parameter->getName()] = $this->createMock($parameter->getType()->getName());
        }
        $this->makeModel = $class->newInstanceArgs($this->parameters);
    }

    public function testMake(): void
    {
        $context = ModelContextTest::createContext();

        $this->expectedCallGenerateOnce('modelInterfaceGenerator', [
            '/tmp/module-api/Api/Data/Menu/ItemInterface.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('searchResultsInterfaceGenerator', [
            '/tmp/module-api/Api/Data/Menu/ItemSearchResultsInterface.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('repositoryInterfaceGenerator', [
            '/tmp/module-api/Api/Menu/ItemRepositoryInterface.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('resourceModelClassGenerator', [
            '/tmp/module/Model/ResourceModel/Menu/Item.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('modelClassGenerator', [
            '/tmp/module/Model/Menu/Item.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('collectionClassGenerator', [
            '/tmp/module/Model/ResourceModel/Menu/Item/Collection.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('repositoryClassGenerator', [
            '/tmp/module/Model/Menu/ItemRepository.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('searchResultsClassGenerator', [
            '/tmp/module/Model/Menu/ItemSearchResults.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('repositoryUnitTestGenerator', [
            '/tmp/module/Test/Unit/Model/Menu/ItemRepositoryTest.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('modelUnitTestGenerator', [
            '/tmp/module/Test/Unit/Model/Menu/ItemTest.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('repositoryIntegrationTestGenerator', [
            '/tmp/module/Test/Integration/Model/Menu/ItemRepositoryTest.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('modelIntegrationTestFixtureGenerator', [
            '/tmp/module/Test/Integration/_files/menu_item.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('modelRollbackIntegrationTestFixtureGenerator', [
            '/tmp/module/Test/Integration/_files/menu_item_rollback.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('repositoryApiFunctionalTestGenerator', [
            '/tmp/module/Test/Api/Model/Menu/ItemRepositoryTest.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('modelDataProviderGenerator', [
            '/tmp/module-graph-ql/Model/Resolver/DataProvider/Menu/Item.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('modelResolverGenerator', [
            '/tmp/module-graph-ql/Model/Resolver/Menu/Item.php',
            $context
        ]);
        $this->expectedCallOnce('diXmlGenerator', 'generateModulePreferences', [
            '/tmp/module/etc/di.xml',
            $context
        ]);
        $this->expectedCallOnce('dbSchemaXmlGenerator', 'generateTable', [
            '/tmp/module/etc/db_schema.xml',
            $context
        ]);
        $this->expectedCallOnce('aclXmlGenerator', 'generateModelResource', [
            '/tmp/module/etc/acl.xml',
            $context
        ]);
        $this->expectedCallOnce('webapiXmlGenerator', 'generateModelRoutes', [
            '/tmp/module/etc/webapi.xml',
            $context
        ]);
        $this->expectedCallOnce('schemaGraphQlsGenerator', 'generateSchema', [
            '/tmp/module-graph-ql/etc/schema.graphqls',
            $context
        ]);

        $this->makeModel->make($context);
        $this->assertSameSize($this->parameters, $this->checkedGenerators);
    }

    protected function expectedCallGenerateOnce($paramName, $data): void
    {
        $this->expectedCallOnce($paramName, 'generate', $data);
    }

    protected function expectedCallOnce($paramName, $method, $data): void
    {
        if (isset($this->checkedGenerators[$paramName])) {
            throw new \RuntimeException('Generator ' . $paramName . ' was already checked');
        }

        $this->parameters[$paramName]
            ->expects($this->once())
            ->method($method)
            ->with(...$data);
        $this->checkedGenerators[$paramName] = true;
    }
}

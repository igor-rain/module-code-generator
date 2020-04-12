<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Make;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Generator\Api\ModelInterfaceGenerator;
use IgorRain\CodeGenerator\Model\Generator\Api\RepositoryInterfaceGenerator;
use IgorRain\CodeGenerator\Model\Generator\Api\SearchResultsInterfaceGenerator;
use IgorRain\CodeGenerator\Model\Generator\Etc\DbSchemaXmlGenerator;
use IgorRain\CodeGenerator\Model\Generator\Etc\DiXmlGenerator;
use IgorRain\CodeGenerator\Model\Generator\Model\CollectionGenerator;
use IgorRain\CodeGenerator\Model\Generator\Model\ModelGenerator;
use IgorRain\CodeGenerator\Model\Generator\Model\RepositoryGenerator;
use IgorRain\CodeGenerator\Model\Generator\Model\ResourceModelGenerator;
use IgorRain\CodeGenerator\Model\Generator\Model\SearchResultsGenerator;
use IgorRain\CodeGenerator\Model\Generator\Test\ModelIntegrationTestFixtureGenerator;
use IgorRain\CodeGenerator\Model\Generator\Test\ModelRollbackIntegrationTestFixtureGenerator;
use IgorRain\CodeGenerator\Model\Generator\Test\ModelUnitTestGenerator;
use IgorRain\CodeGenerator\Model\Generator\Test\RepositoryApiFunctionalTestGenerator;
use IgorRain\CodeGenerator\Model\Generator\Test\RepositoryIntegrationTestGenerator;
use IgorRain\CodeGenerator\Model\Generator\Test\RepositoryUnitTestGenerator;

class Model
{
    /**
     * @var ModelInterfaceGenerator
     */
    private $modelInterfaceGenerator;
    /**
     * @var SearchResultsInterfaceGenerator
     */
    private $searchResultsInterfaceGenerator;
    /**
     * @var RepositoryInterfaceGenerator
     */
    private $repositoryInterfaceGenerator;
    /**
     * @var ResourceModelGenerator
     */
    private $resourceModelClassGenerator;
    /**
     * @var ModelGenerator
     */
    private $modelClassGenerator;
    /**
     * @var CollectionGenerator
     */
    private $collectionClassGenerator;
    /**
     * @var RepositoryGenerator
     */
    private $repositoryClassGenerator;
    /**
     * @var SearchResultsGenerator
     */
    private $searchResultsClassGenerator;
    /**
     * @var DiXmlGenerator
     */
    private $diXmlGenerator;
    /**
     * @var DbSchemaXmlGenerator
     */
    private $dbSchemaXmlGenerator;
    /**
     * @var RepositoryUnitTestGenerator
     */
    private $repositoryUnitTestGenerator;
    /**
     * @var ModelUnitTestGenerator
     */
    private $modelUnitTestGenerator;
    /**
     * @var RepositoryIntegrationTestGenerator
     */
    private $repositoryIntegrationTestGenerator;
    /**
     * @var ModelIntegrationTestFixtureGenerator
     */
    private $modelIntegrationTestFixtureGenerator;
    /**
     * @var ModelRollbackIntegrationTestFixtureGenerator
     */
    private $modelRollbackIntegrationTestFixtureGenerator;
    /**
     * @var RepositoryApiFunctionalTestGenerator
     */
    private $repositoryApiFunctionalTestGenerator;

    public function __construct(
        ModelInterfaceGenerator $modelInterfaceGenerator,
        SearchResultsInterfaceGenerator $searchResultsInterfaceGenerator,
        RepositoryInterfaceGenerator $repositoryInterfaceGenerator,
        ResourceModelGenerator $resourceModelClassGenerator,
        ModelGenerator $modelClassGenerator,
        CollectionGenerator $collectionClassGenerator,
        RepositoryGenerator $repositoryClassGenerator,
        SearchResultsGenerator $searchResultsClassGenerator,
        DiXmlGenerator $diXmlGenerator,
        DbSchemaXmlGenerator $dbSchemaXmlGenerator,
        RepositoryUnitTestGenerator $repositoryUnitTestGenerator,
        ModelUnitTestGenerator $modelUnitTestGenerator,
        RepositoryIntegrationTestGenerator $repositoryIntegrationTestGenerator,
        ModelIntegrationTestFixtureGenerator $modelIntegrationTestFixtureGenerator,
        ModelRollbackIntegrationTestFixtureGenerator $modelRollbackIntegrationTestFixtureGenerator,
        RepositoryApiFunctionalTestGenerator $repositoryApiFunctionalTestGenerator
    ) {
        $this->modelInterfaceGenerator = $modelInterfaceGenerator;
        $this->searchResultsInterfaceGenerator = $searchResultsInterfaceGenerator;
        $this->repositoryInterfaceGenerator = $repositoryInterfaceGenerator;
        $this->resourceModelClassGenerator = $resourceModelClassGenerator;
        $this->modelClassGenerator = $modelClassGenerator;
        $this->collectionClassGenerator = $collectionClassGenerator;
        $this->repositoryClassGenerator = $repositoryClassGenerator;
        $this->searchResultsClassGenerator = $searchResultsClassGenerator;
        $this->diXmlGenerator = $diXmlGenerator;
        $this->dbSchemaXmlGenerator = $dbSchemaXmlGenerator;
        $this->repositoryUnitTestGenerator = $repositoryUnitTestGenerator;
        $this->modelUnitTestGenerator = $modelUnitTestGenerator;
        $this->repositoryIntegrationTestGenerator = $repositoryIntegrationTestGenerator;
        $this->modelIntegrationTestFixtureGenerator = $modelIntegrationTestFixtureGenerator;
        $this->modelRollbackIntegrationTestFixtureGenerator = $modelRollbackIntegrationTestFixtureGenerator;
        $this->repositoryApiFunctionalTestGenerator = $repositoryApiFunctionalTestGenerator;
    }

    /**
     * @param ModelContext $context
     */
    public function make($context): void
    {
        $modelInterfaceFilePath = $context->getModelInterface()->getAbsoluteFilePath();
        $this->modelInterfaceGenerator->generate($modelInterfaceFilePath, $context);

        $searchResultsInterfaceFilePath = $context->getSearchResultsInterface()->getAbsoluteFilePath();
        $this->searchResultsInterfaceGenerator->generate($searchResultsInterfaceFilePath, $context);

        $repositoryInterfaceFilePath = $context->getRepositoryInterface()->getAbsoluteFilePath();
        $this->repositoryInterfaceGenerator->generate($repositoryInterfaceFilePath, $context);

        $resourceModelClassFilePath = $context->getResourceModel()->getAbsoluteFilePath();
        $this->resourceModelClassGenerator->generate($resourceModelClassFilePath, $context);

        $modelClassFilePath = $context->getModel()->getAbsoluteFilePath();
        $this->modelClassGenerator->generate($modelClassFilePath, $context);

        $collectionClassFilePath = $context->getCollection()->getAbsoluteFilePath();
        $this->collectionClassGenerator->generate($collectionClassFilePath, $context);

        $repositoryClassFilePath = $context->getRepository()->getAbsoluteFilePath();
        $this->repositoryClassGenerator->generate($repositoryClassFilePath, $context);

        $searchResultsClassFilePath = $context->getSearchResults()->getAbsoluteFilePath();
        $this->searchResultsClassGenerator->generate($searchResultsClassFilePath, $context);

        $diXmlFilePath = $context->getModule()->getPath() . '/etc/di.xml';
        $this->diXmlGenerator->generatePreference($diXmlFilePath, $context->getModelInterface()->getName(), $context->getModel()->getName());
        $this->diXmlGenerator->generatePreference($diXmlFilePath, $context->getRepositoryInterface()->getName(), $context->getRepository()->getName());
        $this->diXmlGenerator->generatePreference($diXmlFilePath, $context->getSearchResultsInterface()->getName(), $context->getSearchResults()->getName());

        $dbSchemaXmlFileMath = $context->getModule()->getPath() . '/etc/db_schema.xml';
        $this->dbSchemaXmlGenerator->generateTable($dbSchemaXmlFileMath, $context);

        $repositoryUnitTestFilePath = $context->getRepository()->getUnitTest()->getAbsoluteFilePath();
        $this->repositoryUnitTestGenerator->generate($repositoryUnitTestFilePath, $context);

        $modelUnitTestFilePath = $context->getModel()->getUnitTest()->getAbsoluteFilePath();
        $this->modelUnitTestGenerator->generate($modelUnitTestFilePath, $context);

        $repositoryIntegrationTestFilePath = $context->getRepository()->getIntegrationTest()->getAbsoluteFilePath();
        $this->repositoryIntegrationTestGenerator->generate($repositoryIntegrationTestFilePath, $context);

        $modelIntegrationTestFixtureFilePath = $context->getFixtureAbsolutePath('Integration', $context->getEventObjectName());
        $this->modelIntegrationTestFixtureGenerator->generate($modelIntegrationTestFixtureFilePath, $context);

        $modelRollbackIntegrationTestFixtureFilePath = $context->getFixtureAbsolutePath('Integration', $context->getEventObjectName() . '_rollback');
        $this->modelRollbackIntegrationTestFixtureGenerator->generate($modelRollbackIntegrationTestFixtureFilePath, $context);

        $repositoryApiFunctionalTestFilePath = $context->getRepository()->getApiFunctionalTest()->getAbsoluteFilePath();
        $this->repositoryApiFunctionalTestGenerator->generate($repositoryApiFunctionalTestFilePath, $context);
    }
}

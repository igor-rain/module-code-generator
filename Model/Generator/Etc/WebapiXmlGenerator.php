<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Etc;

use DOMElement;
use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;

class WebapiXmlGenerator extends AbstractXmlGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generateModelRoutes(string $fileName, ModelContext $context): void
    {
        $this->generateRoute(
            $fileName,
            '/V1/' . $context->getVariableName() . '/:' . $context->getVariableName() . 'Id',
            'GET',
            $context->getRepositoryInterface()->getName(),
            'getById',
            [$context->getAclResourceName()]
        );
        $this->generateRoute(
            $fileName,
            '/V1/' . $context->getVariableName(),
            'POST',
            $context->getRepositoryInterface()->getName(),
            'save',
            [$context->getAclResourceName()]
        );
        $this->generateRoute(
            $fileName,
            '/V1/' . $context->getVariableName() . '/:' . $context->getVariableName() . 'Id',
            'DELETE',
            $context->getRepositoryInterface()->getName(),
            'deleteById',
            [$context->getAclResourceName()]
        );
        $this->generateRoute(
            $fileName,
            '/V1/' . $context->getVariableName() . '/list',
            'GET',
            $context->getRepositoryInterface()->getName(),
            'getList',
            [$context->getAclResourceName()]
        );
    }

    public function generateRoute(string $fileName, string $url, string $requestMethod, string $class, string $classMethod, array $resources): void
    {
        $source = $this->getSource($fileName);

        $doc = $source->getDocument();

        if (!$this->findRoute($doc->documentElement, $url, $requestMethod)) {
            $route = $doc->createElement('route');
            $route->setAttribute('url', $url);
            $route->setAttribute('method', $requestMethod);
            $doc->documentElement->appendChild($route);

            $service = $doc->createElement('service');
            $service->setAttribute('class', $class);
            $service->setAttribute('method', $classMethod);
            $route->appendChild($service);

            if ($resources) {
                $resourcesNode = $doc->createElement('resources');
                $route->appendChild($resourcesNode);

                foreach ($resources as $resource) {
                    $resourceNode = $doc->createElement('resource');
                    $resourceNode->setAttribute('ref', $resource);
                    $resourcesNode->appendChild($resourceNode);
                }
            }
        }

        $source->save();
    }

    protected function findRoute(DOMElement $routes, $url, $requestMethod): ?DOMElement
    {
        foreach ($routes->childNodes as $route) {
            if (
                $route instanceof DOMElement &&
                $route->tagName === 'route' &&
                $route->getAttribute('url') === $url &&
                $route->getAttribute('method') === $requestMethod
            ) {
                return $route;
            }
        }
        return null;
    }

    protected function getSource(string $fileName): XmlSource
    {
        /** @var XmlSource $source */
        $source = $this->sourceFactory->create($fileName, 'xml');

        if ($source->exists()) {
            $source->load();
        } else {
            $source->getDocument()->loadXML($this->getEmptyTemplate());
        }

        return $source;
    }

    protected function getEmptyTemplate(): string
    {
        return '<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd"></routes>';
    }
}

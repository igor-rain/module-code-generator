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

class AclXmlGenerator extends AbstractXmlGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generateModelResource(string $fileName, ModelContext $context): void
    {
        $this->generateResource($fileName, $context->getAclResourceName(), 'Magento_Backend::admin', [
            'title' => ucfirst($context->getClassDescription()),
            'translate' => 'title',
            'sortOrder' => 10
        ]);
    }

    public function generateResource(string $fileName, string $id, string $parentId, array $attributes): void
    {
        $source = $this->getSource($fileName);

        $doc = $source->getDocument();

        if (!$doc->getElementById($id)) {
            $parentNode = $doc->getElementById($parentId);
            if (!$parentNode) {
                $acl = $this->getOrCreateAclNode($doc->documentElement);
                $resources = $this->getOrCreateResourcesNode($acl);

                $parentNode = $doc->createElement('resource');
                $parentNode->setAttribute('id', $parentId);
                $resources->appendChild($parentNode);
            }

            $resourceNode = $doc->createElement('resource');
            $resourceNode->setAttribute('id', $id);
            foreach ($attributes as $name => $value) {
                $resourceNode->setAttribute($name, $value);
            }
            $parentNode->appendChild($resourceNode);
        }

        $source->save();
    }

    protected function getOrCreateAclNode(DOMElement $config): DOMElement
    {
        $acl = $this->getFirstChildByTagName($config, 'acl');
        if (!$acl) {
            $acl = $config->ownerDocument->createElement('acl');
            $config->appendChild($acl);
        }
        return $acl;
    }

    protected function getOrCreateResourcesNode(DOMElement $acl): DOMElement
    {
        $resources = $this->getFirstChildByTagName($acl, 'resources');
        if (!$resources) {
            $resources = $acl->ownerDocument->createElement('resources');
            $acl->appendChild($resources);
        }
        return $resources;
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
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd"></config>';
    }
}

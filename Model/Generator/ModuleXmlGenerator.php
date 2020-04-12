<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;

class ModuleXmlGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate($fileName, ModuleContext $context)
    {
        /** @var XmlSource $source */
        $source = $this->sourceFactory->create($fileName, 'xml');
        $doc = $source->getDocument();
        $doc->loadXML($this->getData());

        /** @var \DOMElement $module */
        $module = $doc->getElementsByTagName('module')[0];
        $module->setAttribute('name', $context->getName());
        $module->setAttribute('setup_version', $context->getVersion());
        if ($context->getDependencies()) {
            $sequence = $doc->createElement('sequence');
            $module->appendChild($sequence);
            foreach ($context->getDependencies() as $dependency) {
                $moduleInSequence = $doc->createElement('module');
                $moduleInSequence->setAttribute('name', $dependency->getName());
                $sequence->appendChild($moduleInSequence);
            }
        }

        $source->save();
    }

    protected function getData()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module/>
</config>
';
    }
}

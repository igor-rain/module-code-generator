<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Etc;

use DOMElement;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;

class DiXmlGenerator extends AbstractXmlGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generatePreference(string $fileName, string $for, string $type): void
    {
        $source = $this->getSource($fileName);

        $doc = $source->getDocument();

        $preferenceNode = $this->findPreferenceNode($doc->documentElement, $for);
        if ($preferenceNode) {
            $preferenceNode->setAttribute('type', $type);
        } else {
            $preferenceNode = $doc->createElement('preference');
            $preferenceNode->setAttribute('for', $for);
            $preferenceNode->setAttribute('type', $type);
            $doc->documentElement->appendChild($preferenceNode);
        }

        $source->save();
    }

    protected function findPreferenceNode(DOMElement $config, string $for): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($config, 'preference', 'for', [$for, '\'' . $for]);
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
        return '<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd"></config>';
    }
}

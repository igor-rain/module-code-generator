<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use DOMElement;

abstract class AbstractXmlGenerator
{
    /**
     * @param DOMElement $parent
     * @param string $tagName
     * @param string $attributeName
     * @param array|string $attributeValue
     *
     * @return null|DOMElement
     */
    protected function findChildByTagNameAndAttributeValue(DOMElement $parent, $tagName, $attributeName, $attributeValue): ?DOMElement
    {
        if (!is_array($attributeValue)) {
            $attributeValue = [$attributeValue];
        }
        foreach ($parent->childNodes as $child) {
            if (
                $child instanceof DOMElement &&
                $child->tagName === $tagName &&
                in_array($child->getAttribute($attributeName), $attributeValue, true)
            ) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param DOMElement $parent
     * @param $tagName
     *
     * @return null|DOMElement
     */
    protected function getFirstChildByTagName(DOMElement $parent, $tagName): ?DOMElement
    {
        foreach ($parent->childNodes as $child) {
            if (
                $child instanceof DOMElement &&
                $child->tagName === $tagName
            ) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param DOMElement $parent
     * @param $tagName
     *
     * @return null|DOMElement
     */
    protected function getLastChildByTagName(DOMElement $parent, $tagName): ?DOMElement
    {
        $lastChild = null;
        foreach ($parent->childNodes as $child) {
            if (
                $child instanceof DOMElement &&
                $child->tagName === $tagName
            ) {
                $lastChild = $child;
            }
        }

        return $lastChild;
    }
}

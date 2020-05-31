<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Etc;

use DOMElement;

abstract class AbstractXmlGenerator
{
    protected function findChildByTagNameAndAttributeValue(DOMElement $parent, string $tagName, string $attributeName, $attributeValue): ?DOMElement
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

    protected function getFirstChildByTagName(DOMElement $parent, string $tagName): ?DOMElement
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

    protected function getLastChildByTagName(DOMElement $parent, string $tagName): ?DOMElement
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

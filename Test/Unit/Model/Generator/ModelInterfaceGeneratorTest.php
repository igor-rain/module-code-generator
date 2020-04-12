<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator;

use IgorRain\CodeGenerator\Model\Generator\ModelInterfaceGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;

/**
 * @internal
 * @coversNothing
 */
class ModelInterfaceGeneratorTest extends AbstractPhpSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new ModelInterfaceGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1Api\Api\Data\Menu;

/**
 * @api
 */
interface ItemInterface
{
    public const SKU = \'sku\';

    public const NAME = \'name\';

    public const PRICE = \'price\';

    public const ATTRIBUTE_SET_ID = \'attribute_set_id\';

    /**
     * Menu item id
     *
     * @return string|null
     */
    public function getId();

    /**
     * Set menu item id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * Menu item sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set menu item sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Menu item name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set menu item name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Menu item price
     *
     * @return string|null
     */
    public function getPrice();

    /**
     * Set menu item price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Menu item attribute set id
     *
     * @return string|null
     */
    public function getAttributeSetId();

    /**
     * Set menu item attribute set id
     *
     * @param string $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId);
}
';
    }
}

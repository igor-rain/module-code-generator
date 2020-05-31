<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Generator\Api\ModelInterfaceGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractPhpSourceGeneratorTest;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\Api\ModelInterfaceGenerator
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

    public const DESCRIPTION = \'description\';

    public const PRICE = \'price\';

    public const ATTRIBUTE_SET_ID = \'attribute_set_id\';

    public const IS_VISIBLE = \'is_visible\';

    /**
     * Menu item id
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set menu item id
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * Menu item sku
     *
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * Set menu item sku
     *
     * @param string|null $sku
     * @return $this
     */
    public function setSku(?string $sku): self;

    /**
     * Menu item name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set menu item name
     *
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self;

    /**
     * Menu item description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set menu item description
     *
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self;

    /**
     * Menu item price
     *
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * Set menu item price
     *
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self;

    /**
     * Menu item attribute set id
     *
     * @return int|null
     */
    public function getAttributeSetId(): ?int;

    /**
     * Set menu item attribute set id
     *
     * @param int|null $attributeSetId
     * @return $this
     */
    public function setAttributeSetId(?int $attributeSetId): self;

    /**
     * Menu item is visible
     *
     * @return bool|null
     */
    public function getIsVisible(): ?bool;

    /**
     * Set menu item is visible
     *
     * @param bool|null $isVisible
     * @return $this
     */
    public function setIsVisible(?bool $isVisible): self;
}
';
    }
}

<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PhpSourceTest extends TestCase
{
    public function testLoadMissingFile(): void
    {
        $this->expectException('RuntimeException');
        $phpSource = new PhpSource('/tmp/missing-file');
        $phpSource->load();
    }

    public function testLoadSave(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getSamplePhp());

        $phpSource = new PhpSource($fileName);
        $phpSource->load();
        $phpSource->save();

        $this->assertEquals($this->getSamplePhp(), file_get_contents($fileName));
        unlink($fileName);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getSamplePhp(): string
    {
        return '<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Api\Data;

/**
 * @api
 * @since 100.0.2
 */
interface ProductInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SKU = \'sku\';

    const NAME = \'name\';

    const PRICE = \'price\';

    const WEIGHT = \'weight\';

    const STATUS = \'status\';

    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set product id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);
}
';
    }
}

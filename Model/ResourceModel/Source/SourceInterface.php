<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

interface SourceInterface
{
    public function __construct($fileName);

    public function exists(): bool;

    public function load(): void;

    public function save(): void;
}

<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\JsonSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;

class ComposerJsonGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate($fileName, ModuleContext $context): void
    {
        /** @var JsonSource $source */
        $source = $this->sourceFactory->create($fileName, 'json');
        $source->merge($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModuleContext $context): array
    {
        $data = [
            'name' => $context->getComposerPackage(),
            'description' => $context->getDescription(),
            'require' => [
                'php' => '~7.1.3||~7.2.0||~7.3.0',
            ],
            'type' => 'magento2-module',
            'license' => [
                'OSL-3.0',
                'AFL-3.0',
            ],
            'autoload' => [
                'files' => [
                    'registration.php',
                ],
                'psr-4' => [
                    $context->getPsr4Prefix() => '',
                ],
            ],
            'version' => $context->getVersion(),
        ];

        foreach ($context->getDependencies() as $dependencyContext) {
            $data['require'][$dependencyContext->getComposerPackage()] = '~' . $dependencyContext->getVersion();
        }

        return $data;
    }
}

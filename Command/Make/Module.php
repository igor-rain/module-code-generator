<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class Module extends Command
{
    public const NAME = 'dev:make:module';

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription(
                'Generate module'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = new Question('Module name (e.g. Vendor_Module): ');
        $moduleName = $helper->ask($input, $output, $moduleNameQuestion);

        $objectManager = ObjectManager::getInstance();
        $makeModule = $objectManager->get(\IgorRain\CodeGenerator\Model\Make\Module::class);

        $apiQuestion = new ConfirmationQuestion('Create separated module for API? ', false);
        if ($helper->ask($input, $output, $apiQuestion)) {
            $makeModule->makeWithApi($moduleName);
        } else {
            $makeModule->make($moduleName);
        }
    }
}

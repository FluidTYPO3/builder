<?php
namespace FluidTYPO3\Builder\Command;

use FluidTYPO3\Builder\Service\CommandService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FluidSyntaxCommand extends Command
{
    /**
     * @var CommandService
     */
    private $commandService;

    public function __construct(?CommandService $commandService = null)
    {
        parent::__construct();
        $this->commandService = $commandService ?? GeneralUtility::makeInstance(ObjectManager::class)->get(CommandService::class);
    }

    protected function configure()
    {
        $this->setDescription('Builder: check Fluid syntax')
            ->setHelp('Checks the syntax validity of Fluid files (in provided extension, path, or path in extension')
            ->addOption(
                'extension',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Extension key to check, if not specified will check all extensions containing Fluid templates'
            )->addOption(
                'path',
                'p',
                InputOption::VALUE_OPTIONAL,
                'File or folder path (if extensionKey is included, path is relative to this extension)'
            )->addOption(
                'extensions',
                'x',
                InputOption::VALUE_OPTIONAL,
                'If provided, this CSV list of file extensions are considered Fluid templates',
                'html,xml,txt'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extension = $input->getOption('extension');
        $path = $input->getOption('path');
        $extensions = $input->getOption('extensions');
        $verbose = (bool) $input->getOption('verbose');
        $this->commandService->setOutput($output);
        return $this->commandService->checkFluidSyntax($extension, $path, $extensions, $verbose);
    }
}

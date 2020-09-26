<?php
namespace FluidTYPO3\Builder\Command;

use FluidTYPO3\Builder\Service\CommandService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ListCommand extends Command
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
        // bool $json = false
        $this->setDescription('Builder: list extensions')
            ->setHelp('Lists extensions (active, inactive) and supports JSON output')
            ->addOption(
                'detail',
                'd',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, outputs detailed info',
                false
            )->addOption(
                'active',
                'a',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, outputs active (installed) extensions. Can be combined with "inactive".',
                false
            )->addOption(
                'inactive',
                'i',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, outputs inactive (not installed) extensions. Can be combined with "active".',
                false
            )->addOption(
                'json',
                'j',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, outputs information as JSON.',
                false
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $detail = (bool) $input->getOption('detail');
        $active = (bool) $input->getOption('active');
        $inactive = (bool) $input->getOption('inactive');
        $json = (bool) $input->getOption('json');
        $this->commandService->setOutput($output);
        return $this->commandService->listExtensions($detail, $active, $inactive, $json);
    }
}

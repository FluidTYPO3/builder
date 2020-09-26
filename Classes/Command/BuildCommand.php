<?php
namespace FluidTYPO3\Builder\Command;

use FluidTYPO3\Builder\Service\CommandService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class BuildCommand extends Command
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
        $this->setDescription('Builder: generate provider extension')
            ->setHelp('Creates a new Flux provider extension and bootstraps it with assets/classes according to given toggles.')
            ->addArgument(
                'extension',
                InputArgument::REQUIRED,
                'Extension key to generate (lowercase_underscored format)'
            )->addArgument(
                'author',
                InputArgument::REQUIRED,
                'Author (Firstname Lastname email@address.com)'
            )
            ->addOption(
                'title',
                't',
                InputOption::VALUE_OPTIONAL,
                'Title of the resulting extension, by default "Provider extension for $enabledFeaturesList"'
            )->addOption(
                'description',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Description for extension, by default "Provider extension for $enabledFeaturesList"'
            )->addOption(
                'useVhs',
                'vhs',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, adds the VHS extension as dependency - recommended, on by default',
                true
            )->addOption(
                'pages',
                'p',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, generates basic files for implementing Fluid Page templates',
                true
            )->addOption(
                'content',
                'c',
                InputOption::VALUE_OPTIONAL,
                'IF TRUE, generates basic files for implementing Fluid Content templates',
                true
            )->addOption(
                'controllers',
                'cnt',
                InputOption::VALUE_OPTIONAL,
                'If TRUE, generates controllers for each enabled feature',
                true
            )->addOption(
                'dry',
                'dry',
                InputOption::VALUE_OPTIONAL,
                'If TRUE performs a dry run without writing files;reports which files would have been written',
                false
            );
    }

    /**
     * Execute scheduler tasks
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $extension = $input->getArgument('extension');
        $author = $input->getArgument('author');
        $title = $input->getOption('title');
        $description = $input->getOption('title');
        $useVhs = (boolean) $input->getOption('useVhs');
        $pages = (boolean) $input->getOption('pages');
        $content = (boolean) $input->getOption('content');
        $controllers = (boolean) $input->getOption('controllers');
        $verbose = (boolean) $input->getOption('verbose');
        $dry = (boolean) $input->getOption('dry');

        $this->commandService->setOutput($output);
        return $this->commandService->generateProviderExtension(
            $extension,
            $author,
            $title,
            $description,
            $controllers,
            $pages,
            $content,
            $useVhs,
            $dry,
            $verbose
        );
    }
}

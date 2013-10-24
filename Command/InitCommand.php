<?php

namespace Epam\CiBundle\Command;

use Epam\CiBundle\Generator\Build\PhingGenerator;
use Epam\CiBundle\Generator\Ci\JenkinsGenerator;
use Epam\CiBundle\Generator\Deploy\CapifonyGenerator;
use Epam\CiBundle\Generator\InitGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class InitCommand extends GeneratorCommand
{
    const DEFAULT_BUILD_TOOL = 'phing';
    const DEFAULT_CI_SERVER = 'jenkins';
    const DEFAULT_DEPLOY_TOOL = 'capifony';

    const BUNDLE_NAME = 'EpamCiBundle';

    /** @var array */
    private static $supportedBuildTools = array('phing');
    /** @var array */
    private static $supportedCiServers = array('jenkins');
    /** @var array */
    private static $supportedDeployTools = array('capifony');

    /** @var BundleInterface */
    private $bundle;
    /** @var string */
    private $projectDir;
    /** @var string */
    private $appDir;
    /** @var string */
    private $buildTool;
    /** @var string */
    private $ciServer;
    /** @var string */
    private $deployTool;
    /** @var string */
    private $projectName;

    protected function configure()
    {
        $this
            ->setName('epam-ci:init')
            ->setDescription('Initializes CI bundle with necessary files & directories')
            ->addArgument('project-name', InputArgument::REQUIRED)
            ->addOption('dir-app', null, InputOption::VALUE_REQUIRED, 'Path to app directory', 'app')
            ->addOption(
                'build-tool',
                'b',
                InputOption::VALUE_REQUIRED,
                'Tool used to build the project on developer machines and CI server. Supported tools: '
                    . implode(', ', self::$supportedBuildTools),
                self::DEFAULT_BUILD_TOOL
            )
            ->addOption(
                'ci-server',
                'c',
                InputOption::VALUE_REQUIRED,
                'Continuous Integration server used for the project. Supported servers: '
                    . implode(', ', self::$supportedCiServers),
                self::DEFAULT_CI_SERVER
            )
            ->addOption(
                'deploy-tool',
                'd',
                InputOption::VALUE_REQUIRED,
                'Tool used to deploy application build to the target environment. Supported tools: '
                    . implode(', ', self::$supportedDeployTools),
                self::DEFAULT_DEPLOY_TOOL
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->applyArguments($input);

        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('All existing files would be overwritten. Do you confirm initialization', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }
//        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
//            $output->writeln('Test ' . $input->getArgument('project-name'));
//        }
        /** @var InitGenerator $generator */
        $generator = $this->getGenerator($this->bundle);
        $generator->generate();
        $output->writeln('Generating required files & directories: <info>OK</info>');
    }

    protected function createGenerator()
    {
        $skeletonDirs = $this->getSkeletonDirs($this->bundle);
        $filesystem = $this->getContainer()->get('filesystem');

        $buildGenerator = null;
        switch ($this->buildTool) {
            case 'phing':
                $buildGenerator = new PhingGenerator($filesystem, $this->projectName, $this->projectDir, $this->appDir);
                break;
        }
        $buildGenerator->setSkeletonDirs($skeletonDirs);

        $ciGenerator = null;
        switch ($this->ciServer) {
            case 'jenkins':
                $ciGenerator = new JenkinsGenerator($filesystem, $this->projectName, $this->appDir);
                break;
        }
        $ciGenerator->setSkeletonDirs($skeletonDirs);

        $deployGenerator = null;
        switch ($this->deployTool) {
            case 'capifony':
                $deployGenerator = new CapifonyGenerator($filesystem, $this->projectName, $this->projectDir, $this->appDir);
                break;
        }
        $deployGenerator->setSkeletonDirs($skeletonDirs);

        return new InitGenerator($buildGenerator, $ciGenerator, $deployGenerator);
    }

    private function applyArguments(InputInterface $input)
    {
        $buildTool = $input->getOption('build-tool');
        if (!in_array($buildTool, self::$supportedBuildTools)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Specified build tool is not supported: "%s". Supported tools: %s.',
                    $buildTool,
                    implode(', ', self::$supportedBuildTools)
                )
            );
        }
        $this->buildTool = $buildTool;

        $ciServer = $input->getOption('ci-server');
        if (!in_array($ciServer, self::$supportedCiServers)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Specified Continuous Integration server is not supported: "%s". Supported servers: %s.',
                    $ciServer,
                    implode(', ', self::$supportedCiServers)
                )
            );
        }
        $this->ciServer = $ciServer;

        $deployTool = $input->getOption('deploy-tool');
        if (!in_array($deployTool, self::$supportedDeployTools)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Specified deploy tool is not supported: "%s". Supported tools: %s.',
                    $deployTool,
                    implode(', ', self::$supportedDeployTools)
                )
            );
        }
        $this->deployTool = $deployTool;

        $this->projectDir = $this->validateDir(getcwd());
        $this->appDir = $this->validateDir($input->getOption('dir-app'));
        $this->projectName = strtr(Container::underscore(preg_replace('/\s+/', '-', $input->getArgument('project-name'))), '.', '-');
        $this->bundle = $this->getContainer()->get('kernel')->getBundle(self::BUNDLE_NAME);
    }

    private function validateDir($dir)
    {
        if (! file_exists($dir)) {
            throw new \RuntimeException(sprintf('Unable to init CI as the target directory "%s" does not exists.', $dir));
        }
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('Unable to init CI as the target directory "%s" exists but is a file.', realpath($dir)));
        }
        if (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Unable to init CI as the target directory "%s" is not writable.', realpath($dir)));
        }
        return Validators::validateTargetDir(realpath($dir), null, null);
    }
}

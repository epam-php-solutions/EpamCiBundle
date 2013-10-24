<?php

namespace Epam\CiBundle\Generator\Build;

use Epam\CiBundle\Generator\AbstractGenerator;
use Symfony\Component\Filesystem\Filesystem;

class PhingGenerator extends AbstractGenerator
{
    /** @var Filesystem */
    private $filesystem;
    /** @var string */
    private $projectName;
    /** @var string */
    private $projectDir;
    /** @var string */
    private $appDir;

    /**
     * @param Filesystem $filesystem
     * @param string $projectName
     * @param string $projectDir
     * @param string $appDir
     */
    public function __construct(Filesystem $filesystem, $projectName, $projectDir, $appDir)
    {
        $this->filesystem = $filesystem;
        $this->projectName = $projectName;
        $this->projectDir = $projectDir;
        $this->appDir = $appDir;
    }

    public function generate()
    {
        $appDirRelative = rtrim(mb_substr($this->appDir, mb_strlen($this->projectDir)), '/');
        $parameters = array(
            'project_name' => $this->projectName,
            'app_dir' => $appDirRelative,
        );
        $this->renderFile('build/phing/build.xml.twig', $this->projectDir . '/build.xml', $parameters);
        //We have to copy build-epam-ci.xml because otherwise it will lead to error during build-ci (this file will not be available on CI server until composer install is executed)
        $this->renderFile('build/phing/build-epam-ci.xml.twig', $this->projectDir . '/build-epam-ci.xml', $parameters);
        $this->renderFile('build/phing/build.properties.dist.twig', $this->projectDir . '/build.properties.dist', $parameters);
        $this->renderFile('build/phing/phpunit.xml.dist.twig', $this->appDir . '/Resources/configs/phpunit/phpunit.xml.dist', $parameters);
        $this->renderFile('build/phing/phpmd.xml.twig', $this->appDir . '/Resources/configs/phpmd/phpmd.xml', $parameters);
        $this->filesystem->mkdir($this->projectDir . '/build');
        $this->filesystem->touch($this->projectDir . '/build/.gitkeep');
    }
}

<?php

namespace Epam\CiBundle\Generator\Ci;

use Epam\CiBundle\Generator\AbstractGenerator;
use Symfony\Component\Filesystem\Filesystem;

class JenkinsGenerator extends AbstractGenerator
{
    /** @var Filesystem */
    private $filesystem;
    /** @var string */
    private $projectName;
    /** @var string */
    private $appDir;

    /**
     * @param Filesystem $filesystem
     * @param string $projectName
     * @param string $appDir
     */
    public function __construct(Filesystem $filesystem, $projectName, $appDir)
    {
        $this->filesystem = $filesystem;
        $this->projectName = $projectName;
        $this->appDir = $appDir;
    }

    public function generate()
    {
        $parameters = array(
            'project_name' => $this->projectName,
        );
        $this->renderFile('ci/jenkins/config-build-master.xml.twig', $this->appDir . '/Resources/configs/jenkins/config-build-master.xml', $parameters);
        $this->renderFile('ci/jenkins/config-build-package-tag.xml.twig', $this->appDir . '/Resources/configs/jenkins/config-build-package-tag.xml', $parameters);
        $this->renderFile('ci/jenkins/config-deploy-package-tag.xml.twig', $this->appDir . '/Resources/configs/jenkins/config-deploy-package-tag.xml', $parameters);
        $this->renderFile('ci/jenkins/config-deploy-qa-master.xml.twig', $this->appDir . '/Resources/configs/jenkins/config-deploy-qa-master.xml', $parameters);
        $this->filesystem->copy($this->appDir . '/config/parameters.yml.dist', $this->appDir . '/config/parameters_ci.yml', true);
    }
}

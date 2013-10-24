<?php

namespace Epam\CiBundle\Generator\Deploy;

use Epam\CiBundle\Generator\AbstractGenerator;
use Symfony\Component\Filesystem\Filesystem;

class CapifonyGenerator extends AbstractGenerator
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
        $parameters = array(
            'project_name' => $this->projectName,
        );
        $this->renderFile('deploy/capifony/Capfile.twig', $this->projectDir . '/Capfile', $parameters);
        $this->renderFile('deploy/capifony/deploy.rb.twig', $this->appDir . '/Resources/configs/capifony/deploy.rb', $parameters);
        $this->renderFile('deploy/capifony/qa.rb.twig', $this->appDir . '/Resources/configs/capifony/stages/qa.rb', $parameters);
        $this->renderFile('deploy/capifony/maintenance.html.erb.twig', $this->appDir . '/Resources/configs/capifony/maintenance.html.erb', $parameters);
        $this->filesystem->copy($this->appDir . '/config/parameters.yml.dist', $this->appDir . '/config/parameters_ci.yml', true);
        $this->filesystem->copy($this->appDir . '/config/parameters.yml.dist', $this->appDir . '/config/parameters_qa.yml', true);
    }
}

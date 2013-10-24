<?php

namespace Epam\CiBundle\Generator;

class InitGenerator extends AbstractGenerator
{
    /** @var AbstractGenerator */
    private $buildGenerator;
    /** @var AbstractGenerator */
    private $ciGenerator;
    /** @var AbstractGenerator */
    private $deployGenerator;

    public function __construct(AbstractGenerator $buildGenerator, AbstractGenerator $ciGenerator, AbstractGenerator $deployGenerator)
    {
        $this->buildGenerator = $buildGenerator;
        $this->ciGenerator = $ciGenerator;
        $this->deployGenerator = $deployGenerator;
    }

    public function generate()
    {
        $this->buildGenerator->generate();
        $this->ciGenerator->generate();
        $this->deployGenerator->generate();
    }
}

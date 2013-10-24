<?php

namespace Epam\CiBundle\Generator;


use Sensio\Bundle\GeneratorBundle\Generator\Generator;

abstract class AbstractGenerator extends Generator
{
    abstract public function generate();
}

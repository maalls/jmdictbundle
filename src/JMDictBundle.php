<?php 

// src/Acme/TestBundle/AcmeTestBundle.php
namespace Maalls\JMDictBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Maalls\JMDictBundle\DependencyInjection\MaallsJMDictExtension;

class JMDictBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new MaallsJMDictExtension();
    }

}
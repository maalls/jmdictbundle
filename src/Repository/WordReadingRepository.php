<?php

namespace Maalls\JMDictBundle\Repository;

use Maalls\JMDictBundle\Entity\WordReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class WordReadingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WordReading::class);
    }

}

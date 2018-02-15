<?php

namespace Maalls\JMDictBundle\Repository;

use Maalls\JMDictBundle\Entity\KanjiReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class KanjiReadingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, KanjiReading::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('k')
            ->where('k.something = :value')->setParameter('value', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}

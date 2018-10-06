<?php
namespace Maalls\JMDictBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Maalls\JMDictBundle\Service\Text;

class TextTest extends KernelTestCase {
   
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
         
    }

    public function testTokenize()
    {


        $textService = new Text($this->em);
        $tokens = $textService->tokenize("世界からこんにちは！");

        

        $this->assertEquals(4, count($tokens));

    }

}
<?php

namespace Maalls\JMDictBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\WordKanji;
use Maalls\JMDictBundle\Entity\Word;
use Maalls\HeisigBundle\Lib\Text;
use Maalls\HeisigBundle\Entity\Heisig;

class UpdateFuriganaCommand extends Command
{

    protected $em;

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em)
    {

        $this->em = $em;
        parent::__construct();

    }
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('maalls:jmdict:update-furigana')

            // the short description shown while running "php bin/console list"
            ->setDescription('Update furigana.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command update the furigana...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

       $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        //ini_set('memory_limit','-1');
        $output->writeln("Starting loading.");
        $text = new Text();

        $total = $this->em->getRepository(Word::class)->createQueryBuilder("w")
                ->select("count(w.id)")
                ->leftJoin("w.wordKanjis", "wc")
                ->where("w.type = 'kanji'")
                ->getQuery()->getSingleScalarResult();
        $done = 0;
        $offset = 0;

        do {
            
            

            $q = $this->em->getRepository(Word::class)->createQueryBuilder("w")
                ->leftJoin("w.wordKanjis", "wk")
                ->where(" w.type = 'kanji'")
                ->setMaxResults(100)
                ->setFirstResult($offset)
                ->getQuery();

            $words = $q->getResult();
            //var_dump(count($words));
            $output->writeln("$offset / $total");

            foreach($words as $word) {

                echo $word->getValue() . PHP_EOL;

                echo $word->getReading() . PHP_EOL;

                //

            }

            $offset += 100;

        }
        while($words);
        

            
    }

}
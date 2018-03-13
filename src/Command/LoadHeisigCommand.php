<?php

namespace Maalls\JMDictBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\WordKanji;
use Maalls\JMDictBundle\Entity\Word;
use Maalls\HeisigBundle\Lib\Text;
use Maalls\HeisigBundle\Entity\Heisig;

class LoadHeisigCommand extends Command
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
            ->setName('maalls:jmdict:load-heisig')

            // the short description shown while running "php bin/console list"
            ->setDescription('Load heisig.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command load the Heisig...');
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
                ->where("w.hasWordKanjis is null and w.type = 'kanji'")
                ->getQuery()->getSingleScalarResult();
        $done = 0;

        do {
            
            

            $q = $this->em->getRepository(Word::class)->createQueryBuilder("w")
                ->leftJoin("w.wordKanjis", "wk")
                ->where("w.hasWordKanjis is null and w.type = 'kanji'")
                ->setMaxResults(100)
                ->getQuery();

            $words = $q->getResult();
            //var_dump(count($words));
            $output->writeln("$done / $total");

            foreach($words as $word) {

                
                $kanjis = array_unique($text->splitKanjis($word->getValue()));
                
                foreach($kanjis as $kanji) {

                    $heisig = $this->em->getRepository(Heisig::class)->findOneByKanji($kanji);

                    
                    $wordKanji = new WordKanji();
                    $wordKanji->setHeisig($heisig);
                    $wordKanji->setWord($word);
                    $wordKanji->setKanji($kanji);

                    $this->em->persist($wordKanji);

                }

                $word->setHasWordKanjis(true);
                $this->em->persist($word);

            }

            $this->em->flush(); 
            $this->em->clear();
            $done += count($words);

        }
        while($words);

            
    }

}
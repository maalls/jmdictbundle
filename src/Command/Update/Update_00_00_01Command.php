<?php

namespace Maalls\JMDictBundle\Command\Update;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\SensePartOfSpeech;
use Maalls\JMDictBundle\Entity\Sense;
use Maalls\JMDictBundle\Entity\PartOfSpeech;

use Maalls\HeisigBundle\Entity\Heisig;

class Update_00_00_01Command extends Command
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
            ->setName('maalls:jmdict:update:0.0.1')

            // the short description shown while running "php bin/console list"
            ->setDescription('Generate sense pos.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->em;

        $fh = fopen(__dir__ . "/../../../data/pos/edict.csv", "r");

        $posMap = [];

        fgetcsv($fh);

        while($row = fgetcsv($fh)) {

            $posMap[$row[0]] = ["value" => @$row[1], "sub_category" => @$row[2]];

        }

        

        foreach($posMap as $edict => $p) {


            $partOfSpeech = $this->em->getRepository(PartOfSpeech::class)
                ->findOneBy(["value" => $p["value"], "sub_category" => $p["sub_category"]]);

            if(!$partOfSpeech) {

                $partOfSpeech = new PartOfSpeech();
                $partOfSpeech->setValue($p["value"]);
                $partOfSpeech->setSubCategory($p["sub_category"]);
                

            }
            $partOfSpeech->setEdicPos($edict);
            $em->persist($partOfSpeech);
                $em->flush();

            $posMap[$edict]["object"] = $partOfSpeech;

        }

        




        do {

            $output->writeln("batch " . time());
            $senses = $this->em->getRepository(Sense::class)->createQueryBuilder("s")
                ->leftJoin("s.SensePartOfSpeech", "sp")
                ->where("sp.id is null")
                ->setMaxResults(1000)
                ->getQuery()
                ->getResult();

            foreach($senses as $sense) {

                $pos = $sense->getPos();

                $pos = preg_replace("/phrases, clauses, etc./", "phrases/clauses/etc.", $pos);
                $pos = $pos;
                $poses = array_unique(explode(",", $pos));

                foreach($poses as $pos) {

                    $pos = trim($pos);
                    $sensePartOfSpeech = new SensePartOfSpeech();
                    $sensePartOfSpeech->setSense($sense);

                    if(!isset($posMap[$pos])) {

                        throw new \Exception("Shouldn't happen. [$pos]");

                    }

                    $partOfSpeech = $posMap[$pos]["object"];
                    $sensePartOfSpeech->setPartOfSpeech($partOfSpeech);
                    $sensePartOfSpeech->setJdicPos($pos);
                    $em->persist($sensePartOfSpeech);

                }


            }

            $em->flush();
            $em->clear(SensePartOfSpeech::class);
            $em->clear(Sense::class);

            

        }
        while($senses);

    }

}

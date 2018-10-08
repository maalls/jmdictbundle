<?php

namespace Maalls\JMDictBundle\Command\Update;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\SensePartOfSpeech;
use Maalls\JMDictBundle\Entity\Sense;
use Maalls\JMDictBundle\Entity\PartOfSpeech;
use Maalls\JMDictBundle\Entity\EdictPos;

use Maalls\HeisigBundle\Entity\Heisig;

class Update_00_00_02Command extends Command
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
            ->setName('maalls:jmdict:update:0.0.2')

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

            //$posMap[$row[0]] = ["value" => @$row[1], "sub_category" => @$row[2]];

            $pos = $em->getRepository(PartOfSpeech::class)->findOneBy(
                ["value" => @$row[1], "sub_category" => @$row[2]]
            );

            if(!$pos) {

                $pos = new PartOfSpeech();
                $pos->setValue(@$row[1]);
                $pos->setSubCategory(@$row[2]);
                $em->persist($pos);


            }

            $epos = $em->getRepository(EdictPos::class)->findOneBy(
                ["edict_pos" => $row[0]]
            );

            if(!$epos) {

                $epos = new EdictPos();
                $epos->setEdictPos($row[0]);

            }

            $epos->setPartOfSpeech($pos);

            $em->persist($epos);


        }

        $em->flush();

        $stmt = $em->getConnection()->prepare("

            select 
                sp.id as id, sp.part_of_speech_id spos_id, sp.jdic_pos, ep.part_of_speech_id epos_id
            from 
                sense_part_of_speech sp 
            join 
                edict_pos ep 
            on 
                ep.edict_pos = sp.jdic_pos

            where 
                sp.part_of_speech_id != ep.part_of_speech_id

        ");
        
        $stmt->execute();

        while($row = $stmt->fetch()) {

            var_dump($row);
            $stmt2 = $em->getConnection()->prepare("
                update 
                    sense_part_of_speech
                set 
                    part_of_speech_id = ? 
                where 
                    id = ?
            ");

            $stmt2->execute([$row["epos_id"], $row["id"]]);


        }
        

    }

}

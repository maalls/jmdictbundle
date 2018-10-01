<?php

namespace Maalls\JMDictBundle\Command\Update;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\WordKanji;
use Maalls\JMDictBundle\Entity\Word;
use Maalls\HeisigBundle\Lib\Text;
use Maalls\HeisigBundle\Entity\Heisig;

class GenerateWordReadingCommand extends Command
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
            ->setName('maalls:jmdict:update:generate-word-reading')

            // the short description shown while running "php bin/console list"
            ->setDescription('Generate WordReading');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // loop over all the words
        // based on word type:
        // if word type is reading:
        //   - if the word is in hiragana, set the reading as same word
        //   - if the word is in katagana, find the word with same base but in hiragana
        //   - else, set the reading as the same word
        // if the word type is kanji:
        //   - find all the reading word with same base
        //   - filter word that are hiragana
        //   - create a wordReading for each resulting combination.


        $insert = $this->em->getConnection()->prepare("
            insert into word_reading (word_id, reading_id, code) value (?,?, ?)
        ");

        $findByBaseAndValue = $this->em->getConnection()->prepare("
            select 
                *
            from 
                word 
            where 
                base_id = ? and value = ?
        ");

        $findReadingByBase = $this->em->getConnection()->prepare("
            select 
                *
            from 
                word 
            where 
                base_id = ? and type = 'reading'
        ");

        $total = 0;

        do {
            $stmt = $this->em->getConnection()->prepare("
                select 
                    w.*
                from 
                    word w
                left join 
                    word_reading wr
                on 
                    w.id = wr.word_id
                where 
                    wr.id is null 
                limit 1000");

            $stmt->execute();

            $count = 0;

            while($row = $stmt->fetch()) {

                if($row["type"] == "reading") {

                    $hiragana = mb_convert_kana($row["value"], "cH");

                    
                    if($row["value"] == $hiragana) {

                        //echo $row["value"] . " - " . $hiragana . PHP_EOL;
                        $insert->execute([$row["id"], $row["id"], 0]);

                    }
                    else {

                        $findByBaseAndValue->execute([$row["base_id"], $hiragana]);

                        $matches = $findByBaseAndValue->fetchAll();

                        if(!$matches) {

                            // probably some katagana stuff
                            $insert->execute([$row["id"], $row["id"], 1]);

                        }
                        elseif(count($matches) == 1) {

                            // expected behavior
                            $reading = $matches[0];

                            $insert->execute([$row["id"], $reading["id"], 2]);


                        }
                        else {

                            echo "Ooops, unexpected match count for " . $hiragana . " " . $row["value"] . " " . $row["id"] . " : " . count($matches) . PHP_EOL;

                            exit;

                        }

                    }

                }
                else {

                    $findReadingByBase->execute([$row["base_id"]]);

                    $matches = $findReadingByBase->fetchAll();

                    $hiraganas = [];

                    foreach($matches as $match) {

                        $hiragana = mb_convert_kana($match["value"], "cH");
                        //echo $match["value"] . " - " . $hiragana . PHP_EOL;

                        if($match["value"] == $hiragana) {

                            $hiraganas[] = $match;

                        }

                    }

                    
                    if(!count($hiraganas)) {

                        if($matches) {

                            foreach($matches as $match) {

                                $insert->execute([$row["id"], $match["id"], 4]);

                            }

                        }
                        else {

                            echo "Ooops, no hiragana match " . $row["value"] . " " . $row["id"] . PHP_EOL;
                            exit;

                        }
                        


                    }
                    else {

                        foreach($hiraganas as $hiragana) {

                            $insert->execute([$row["id"], $hiragana["id"], 3]);

                        }

                    }


                }

                $count++;

            }

            $total += $count;
            echo $total . PHP_EOL;

        }
        while($count);



    }

}
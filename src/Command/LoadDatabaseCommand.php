<?php

namespace Maalls\JMDictBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\Base;
use Maalls\JMDictBundle\Entity\Word;
use Maalls\JMDictBundle\Entity\KanjiReading;
use Maalls\JMDictBundle\Entity\Sense;
use Maalls\JMDictBundle\Entity\SenseGlossary;
use Maalls\JMDictBundle\Entity\SenseReference;
use Maalls\JMDictBundle\Entity\SenseSource;
use Maalls\JMDictBundle\Entity\SenseWord;



class LoadDatabaseCommand extends Command
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
            ->setName('maalls:jmdict:load-database')

            // the short description shown while running "php bin/console list"
            ->setDescription('Load the database.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command load the JMdict.xml into the database...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        gc_enable();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        //ini_set('memory_limit','-1');
        $output->writeln("Starting loading.");

        $this->xml = simplexml_load_string(file_get_contents(__dir__ . "/../../data/JMdict_e.xml"));
        $this->output = $output;
        //$this->loadWords($output);

        $output->writeln("Setting cross references.");

        $this->loadCrossReferences($output);

        $output->writeln("done.");

            
    }

    function loadCrossReferences($output)
    {

        $q = $this->em->createQuery('delete from Maalls\JMDictBundle\Entity\SenseReference sr where sr.id <> 0');
        $numDeleted = $q->execute();
        $output->writeln($numDeleted . " SenseReference deleted.");

        $count = 0;

        foreach($this->xml->entry as $entry) {

            $count++;

            if($count % 1000 == 0) {

                $this->em->flush();
                $this->em->clear();
                gc_collect_cycles();
                $output->writeln("$count, " . round(memory_get_usage(true) / 1048576) . "Mb");

            }

            $senses = false;
            $ele_senses = $this->to_array($entry->sense);            

            foreach($ele_senses as $ele_sense) {

                if($ele_sense->xref || $ele_sense->ant) {

                    $senses = $this->em->getRepository(Sense::class)->createQueryBuilder("s")
                        ->join("s.senseWords", "sw")
                        ->join("sw.word", "w")
                        ->andWhere("w.base = :sequence_id")->setParameter("sequence_id", $entry->ent_seq)
                        ->getQuery()
                        ->getResult();
                    break;

                }

            }

            if($senses) {

                foreach($ele_senses as $key => $ele_sense) {

                    if(!isset($senses[$key])) {

                        throw new \Exception("Sense key $key is missing. " . count($senses));

                    }

                    $sense = $senses[$key];

                    $this->createReferences($sense, $ele_sense->xref, "synonym");
                    $this->createReferences($sense, $ele_sense->ant, "antinomy");

                }


            }

        }

        $this->em->flush();
        $this->em->clear();
        $output->writeln("Done.");

    }

    function createReferences($sense, $element, $type)
    {

        if($element) {

            foreach($this->to_array($element) as $ref) {

                $values = explode("・", $ref);
                //$output->writeln($entry->ent_seq . " : synonym " . $ref);

                $word = false;
                $relatedSense = false;

                $senseReference = new SenseReference();
                $senseReference->setSense($sense);
                $senseReference->setType($type);


                switch(count($values)) {

                    case 1:

                        //$output->writeln("Word.");
                        $value = $values[0];
                        $words = $this->em->getRepository(Word::class)->findBy(["value" => $value]);

                        if(!$words) {

                            //$output->writeln("No word found for $value skipping.");

                        }
                        else {

                            if(count($words) > 1) {
                                
                                $senseReference->setToCheck(true);

                            }

                            $senseReference->setWord($words[0]);
                            $this->em->persist($senseReference);

                        }
                        break;

                    case 2:

                        if(preg_match("/([0-9]+)/uis", $values[1], $match)) {

                            $value = $values[0];
                            $num = $match[1];
                            //$output->writeln("Word $value and sense $num");
                            
                            $words = $this->em->getRepository(Word::class)->findBy(["value" => $value]);

                            if(!$words) {

                                //$output->writeln("No word found for $value skipping.");

                            }
                            else {

                                if(count($words) > 1) {
                                    
                                    //$output->writeln(count($words) . " words found. Which one is it?");
                                    $senseReference->setToCheck(true);
                                    $word = false;

                                    foreach($words as $k => $w) {

                                        if(count($w->getSenseWords()) >= $num) {

                                            //$output->writeln("taking the one index $k");
                                            $word = $w;
                                            break;

                                        }

                                    }

                                    if(!$word) {

                                        throw new \Exception("All the words don't have enough sense.");

                                    }

                                }
                                else {

                                    $word = $words[0];

                                }
                                
                                $senseReference->setWord($word);
                                $sense = $this->findRelatedSense($word, $num);

                                if($sense) {

                                    $senseReference->setSense($sense);
                                    $this->em->persist($senseReference);

                                }

                            }


                        }
                        else {

                            
                            $word = $this->findWordByKanjiAndReading($values[0], $values[1]);

                            if($word) {

                                $senseReference->setWord($word);
                                $this->em->persist($senseReference);

                            }

                        }

                        break;

                    case 3:
                        //$output->writeln("Kanji, reading and sense.");

                        $word = $this->findWordByKanjiAndReading($values[0], $values[1]);

                        if($word) {

                            $senseReference->setWord($word);
                            $sense = $this->findRelatedSense($word, $values[2]);

                            if($sense) {

                                $senseReference->setRelatedSense($sense);
                                $this->em->persist($senseReference);

                            }

                            
                        }

                        break;

                }

            }

        }


    }

    function findRelatedSense($word, $num)
    {

        $senseWords = $word->getSenseWords();
        
        if(!$senseWords) {

            throw new \Exception("No sense associated with $value");

        }
        elseif(count($senseWords) < $num) {
            
            $this->output->writeln("Ignoring unmatching sense " . $word->getValue());


        }
        else {

            $senseWord = $senseWords[$num - 1];
            return $senseWord->getSense();

        }

    }

    function findWordByKanjiAndReading($kanji, $reading)
    {

        $words = $this->em->getRepository(Word::class)->findBy(["value" => $kanji]);

        if(!$words) {

            $exceptions = ["ブロードノーズ", "イエローテール"];
            if(!in_array($kanji, $exceptions)) {
            
                $this->output->writeln($kanji . " has no match. [l324]");
                return null;
                throw new \Exception($kanji . " has no match.");
            
            }
            else {

                return false;

            }

        }
        elseif(count($words) > 1) {


            $ids = [];
            foreach($words as $word) {

                $ids[] = $word->getBase()->getId();

            }

            $ws = $this->em->getRepository(Word::class)->findBy(["base" => $ids, "value" => $reading]);

            if(!$ws) {

                throw new \Exception($reading . " has no match.");

            }
            elseif(count($ws) > 1) {

                $exceptions = ["木目・きめ", "拍手・はくしゅ", "元・もと", "属する・ぞくする", "興し・おこし", " 泡立つ・あわだつ", "属する・ぞくする",
                    "泡立つ・あわだつ", "薄目・うすめ"];

                $this->output->writeln("Ignoring multiple case " . $kanji);
                return false;


            }
            else {

                $w = $ws[0];

                foreach($words as $word) {

                    if($word->getBase()->getId() == $w->getBase()->getId()) {

                        return $word;
                        break;

                    }

                }

            }

            

        }
        else {

           return $words[0];

        }

    }

    function loadWords($output) 
    {

        $q = $this->em->createQuery('delete from Maalls\JMDictBundle\Entity\Base b where b.id <> 0');
        $numDeleted = $q->execute();

        $output->writeln($numDeleted . " exisiting entry deleted.");

       

        $count = 1;
        $time = microtime(true);
        foreach($this->xml->entry as $entry) {

            //$base = [];
            //echo $entry->ent_seq . " ";
            $base = new Base();
            $base->setId((int)$entry->ent_seq);

            $k_eles = $this->to_array($entry->k_ele);
            $r_eles = $this->to_array($entry->r_ele);

            if(count($k_eles)) {

                $base->setValue($k_eles[0]->keb);

            }
            else {

                $base->setValue($r_eles[0]->reb);

            }

            $this->em->persist($base);


            $k_words = [];
            $r_words = [];

            foreach($k_eles as $ele) {

                $word = new Word();
                $word->setBase($base);
                $word->setType("kanji");
                $word->setValue($ele->keb);
                $word->setInfo($this->get_info($ele->ke_inf));
                $word->setNoKanji(false);
                $this->setPri($ele->ke_pri, $word);
                
                $this->em->persist($word);

                $k_words[] = $word;

            }

            foreach($r_eles as $key => $ele) {

                $word = new Word();
                $word->setBase($base);
                $word->setType("reading");
                $word->setValue($ele->reb);
                $word->setInfo($this->get_info($ele->re_inf));
                $word->setNoKanji(isset($ele->re_nokanji) ? true : false);
                $this->setPri($ele->re_pri, $word);

                $this->em->persist($word);

                $kanjiReadings = $this->findValuesOrAll($ele->re_restr, $k_words);

                /*if(count($kanjiReadings) == 0) {

                    throw new \Exception("No Kanji reading for " . $base->getValue());

                }*/



                foreach($kanjiReadings as $k_word) {

                    $kanjiReading = new kanjiReading();
                    $kanjiReading->setKanji($k_word);
                    $kanjiReading->setReading($word);
                    $this->em->persist($kanjiReading);

                }

                $r_words[] = $word;

            }


            $ele_senses = $this->to_array($entry->sense);
            $pos = '';

            foreach($ele_senses as $ele_sense) {

                $sense = new Sense();
                $sense->setBase($base);
                $pos = $ele_sense->pos ? $this->implode($ele_sense->pos) : $pos;

                $sense->setPos($pos);

                $sense->setField($this->implode($ele_sense->field));
                $sense->setMisc($this->implode($ele_sense->misc));
                $sense->setDial($this->implode($ele_sense->dial));
                $sense->setInfo($this->implode($ele_sense->info));

                $this->em->persist($sense);

                $senseWords = $this->findValuesOrAll($ele_sense->stagk, $k_words);
                $senseWords = array_merge($senseWords, $this->findValuesOrAll($ele_sense->stagr, $r_words));

                if(count($senseWords) == 0) {

                    throw new \Exception("No words associated with sense for " . $base->getValue());

                }

                foreach($senseWords as $word) {

                    $senseWord = new SenseWord();
                    $senseWord->setSense($sense);
                    $senseWord->setWord($word);
                    $this->em->persist($senseWord);

                }



                if($ele_sense->gloss) {

                    foreach($this->to_array($ele_sense->gloss) as $gloss) {

                        $senseGlossary = new SenseGlossary();
                        $senseGlossary->setSense($sense);
                        $senseGlossary->setGlossary(trim($gloss));
                        $this->em->persist($senseGlossary);

                    }

                }

                if($ele_sense->lsource) {

                    foreach($this->to_array($ele_sense->lsource) as $lsource) {

                        $source = new SenseSource();
                        $source->setSense($sense);
                        $source->setLanguage("eng");

                        foreach($lsource->attributes("xml", true) as $key => $value) {

                            if($key == "lang") {

                                $source->setLanguage($value);
                                break;

                            }

                        }

                        $source->setSource((string) $lsource);

                        $this->em->persist($source);

                    }

                }


            }

            $batch_size = 100;
            if($count % $batch_size == 0) {

                $batch_time = microtime(true) - $time;
                $time_per_entry = round(($batch_time / $batch_size), 2);
                $output->writeln($count . " done. " . round(memory_get_usage(true) / 1048576) . "Mb, " . $time_per_entry . "e/s");
                $this->em->flush();
                $this->em->clear();
                gc_collect_cycles();

                $time = microtime(true);

            }

            $count++;
            unset($base);

        }

        $this->em->flush();
        $this->em->clear();

    }

    function findValuesOrAll($element, $words)
    {

        if($element) {

            return $this->findValues($element, $words);

        }
        else {

            return $words;

        }

    }

    function findValues($element, $words)
    {

        $result = [];

        foreach($this->to_array($element) as $value) {

            $result[] = $this->findValue($value, $words);

        }

        return $result;

    }

    function findValue($value, $words)
    {
        $found = false;

        foreach($words as $word) {

            if(trim($word->getValue()) == trim($value)) {

                return $word;

            }

        }

        throw new \Exception("Unable to find $value");

    }

    function get_info($element) {

        return implode(",", $this->to_array($element));

    }

    function setPri($element, $word)
    {

        $pri = $this->get_pri($element);
        $word->setNewsLevel($pri["news"]);
        $word->setIchiLevel($pri["ichi"]);
        $word->setSpeLevel($pri["spe"]);
        $word->setGaiLevel($pri["gai"]);
        $word->setFrequencyLevel($pri["frequency"]);


    }

    function get_pri($element) {

        $word = [];
        $pris = $this->to_array($element);

        $refs = ["news", "ichi", "spe", "gai"];

        foreach($refs as $ref) {

            if(in_array($ref . "1", $pris)) {

                $word[$ref] = 1;

            }
            elseif(in_array($ref . "2", $pris)) {

                $word[$ref] = 2;
            }
            else {

                $word[$ref] = 3;

            }

        }

        $word["frequency"] = 99;
        foreach($pris as $pri) {

            //echo $word["sequence_id"] . " : " . count($k_eles) . " " .  $pri . PHP_EOL;
            if (preg_match("/nf([0-9]+)/ius", $pri, $match)) {

                $word["frequency"] = $match[1];
                break;

            }

        }

        return $word;


    }

    function implode($element, $sep = ",") 
    {

        return implode($sep, $this->to_array($element)); 

    }

    function to_array($values) {

        $array = [];

        if(is_iterable($values)) {

            foreach($values as $value) {

                $array[] = $value;

            }

        }
        elseif($values) {

            //echo "Not Here" . PHP_EOL;

            $array[] = $values;

        }

        return $array;
        
    }


}
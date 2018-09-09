<?php

namespace Maalls\JMDictBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maalls\JMDictBundle\Entity\WordKanji;
use Maalls\JMDictBundle\Entity\Word;
use Maalls\HeisigBundle\Lib\Text;
use Maalls\HeisigBundle\Entity\Heisig;

class LoadJlptCommand extends Command
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
            ->setName('maalls:jmdict:load-jlpt')

            // the short description shown while running "php bin/console list"
            ->setDescription('Load JLPT.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command load the JLPT...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $hiragana_map_data = explode(PHP_EOL, file_get_contents(__dir__ . "/hiragana_map.txt"));

        $hiragana_map = [];

        foreach($hiragana_map_data as $line) {
            if(trim($line)) {
                list($value, $world_id) = explode("=>", $line);
                $hiragana_map[$value] = $world_id;
            }

        }

        
        $em = $this->em;
        $rep = $this->em->getRepository(Word::class);
        $fh = fopen(__dir__ . "/../../data/jlpt.txt", "r");

        $level = '';
        $no_kanji = 0;
        $bad_count = 0;
        $count = 0;

        while($line = fgets($fh)) {

            $count++;
            if(!trim($line)) continue;
            //echo $line . PHP_EOL;

            if(preg_match("/level ([0-9]+)/ius", $line, $match)) {

                $level = $match[1];

            }
            else {
                preg_match_all("/ja\|([^}]+)/ius", $line, $match);

                if(isset($match[1]) && $match[1]) {

                    
                    if(count($match[1]) == 1) {

                        // only hiragana is available
                        $value = $match[1][0];

                        $words = $rep->findBy(["value" => $value]);

                        // hiragana only, easy set the value
                        if(count($words) == 1) {

                            $word = $words[0];
                            
                            if(!$word->getJlptLevel()) {
                                $word->setJlptLevel($level);
                                $em->persist($word);
                                $em->flush();
                            }


                        }
                        elseif(count($words) > 1) {

                            //echo $no_kanji . " " . $value . " " . $line . PHP_EOL;
                            $no_kanji++;
                            /*
                            TODO
                            need to be done manually by checking word meaning.
                            */
                            preg_match("/-(.*)$/uis", $line, $match);
                         
                            $meanings = explode(", ", $match[1]);
                            $meaning = trim($meanings[0]);

                            echo "--------------------" . PHP_EOL;
                            echo $value . " " . $match[1] . " :" . PHP_EOL;
                                
                            if(isset($hiragana_map[$value . $meaning])) {

                                echo "In file" . PHP_EOL;
                                $k = $hiragana_map[$value . $meaning];

                                $word = $words[$k];

                                if(!$word->getJlptLevel()) {

                                    $word->setJlptLevel($level);
                                    $em->persist($word);
                                    $em->flush();

                                }

                                continue;

                            }

                            $autodetected = false;

                            foreach($words as $k => $word) {

                                echo " - ($k) " . $word->getId() . " " . $word->getBase()->getValue() . " " . $word->getFrequencyLevel() . PHP_EOL;

                                foreach($word->getSenseWords() as $senseWord) {


                                    foreach($senseWord->getSense()->getSenseGlossaries() as $glossary)
                                    {

                                        echo "   - " . $glossary->getGlossary() . PHP_EOL;

                                        if(mb_strstr($glossary->getGlossary(), $meaning) || mb_strstr($meaning, $glossary->getGlossary())) {

                                            echo "autodetected." . PHP_EOL;
                                            $autodetected = true;
                                            $word->setJlptLevel($level);
                                            $em->persist($word);
                                            $em->flush();

                                        }

                                    }

                                }

                            }

                            if(!$autodetected) {

                                $k = readline("which one");
                                echo "Setting to " . $words[$k]->getId() . PHP_EOL;
                                

                                $word = $words[$k];

                                $word->setJlptLevel($level);
                                $em->persist($word);
                                $em->flush();

                                file_put_contents(__dir__ . "/hiragana_map.txt", $value . $meaning . "=>" . $k . PHP_EOL, FILE_APPEND);

                            }
                            //echo $value . " => " . count($words) . PHP_EOL;
                            

                        } 

                    } 
                    elseif(count($match[1]) == 2) {

                        // kanji and hiragana
                        $kanji = $match[1][0];
                        $hiragana = $match[1][1];

                        // get the words that has proper kanji and reading and assign level

                        $wordFromKanjis = $rep->findBy(["value" => $kanji]);

                        $wordFromHiraganas = $rep->findBy(["value" => $hiragana]);

                        $match = [];

                        foreach($wordFromKanjis as $wfk) {

                            foreach($wordFromHiraganas as $wfh) {

                                if($wfk->getBase()->getId() == $wfh->getBase()->getId()) {

                                    $match[] = [$wfk, $wfh];

                                }

                            }

                        }

                        if(!$wordFromKanjis || !$wordFromHiraganas) { 

                            throw new \Exception("Can't find both.");

                        }
                        if(!$match) {

                            echo "No matching pair: " . $kanji . " " . $hiragana . PHP_EOL; 

                        }
                        else {

                            //echo "$count updating " . $kanji  . " " . $hiragana . " to level $level" . PHP_EOL;

                            $update = false;
                            // for each match we apply the level!
                            foreach($match as $pair) {

                                foreach($pair as $word) {

                                    if(!$word->getJlptLevel()) {
                                        $word->setJlptLevel($level);
                                        $em->persist($word);
                                        $update = true;
                                    }

                                }

                            }

                            if($update) {

                                $em->flush();
                                $em->clear();

                            }

                        }
                        /*if(count($wordFromKanji) > 1 && count($wordFromHiragana) > 1) {

                            echo "NO: " . count($wordFromKanji) . ": " . $kanji . "-" . count($wordFromHiragana) . " : " . $hiragana . PHP_EOL;

                        }*/

                        //echo $kanji . " " . $hiragana . PHP_EOL;


                    }
                    else {
                        $bad_count++;
                        $msg = "Unepected $level " . count($match[1]) . " "  . implode(",", $match[1]);
                        echo $msg . PHP_EOL;
                        //throw new \Exception($msg);

                    }


                    // if kanji, assign to matching kanji

                    // if kanji and furigana, assign to furigana that match the assigned kanji.

                    // if furigana only, retrieve for db and dump if multiple for now.

                }
                else {

                    var_dump($match);
                    throw new \Exception("Unexpected match for " . $line);

                }
                

            }
           

        }

        echo "Done, no kanji:" . $no_kanji . ", bad count:" . $bad_count . PHP_EOL;

        
    }

}
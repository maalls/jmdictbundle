<?php

namespace Maalls\JMDictBundle\Repository;

use Maalls\JMDictBundle\Entity\WordReading;
use Maalls\JMDictBundle\Entity\Word;
use Maalls\JMDictBundle\Entity\WordKanji;
use Maalls\JMDictBundle\Entity\Sense;
use Maalls\JMDictBundle\Entity\SenseWord;
use Maalls\JMDictBundle\Entity\SenseGlossary;
use Maalls\JMDictBundle\Entity\SensePartOfSpeech;
use Maalls\JMDictBundle\Entity\Base;
use Maalls\HeisigBundle\Lib\Text;
use Maalls\HeisigBundle\Entity\Heisig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class WordReadingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WordReading::class);
    }

    public function generate($surface, $hiragana, $partOfSpeech, $glossary)
    {

        $em = $this->getEntityManager();
        $words = $em->getRepository(Word::class)->findBy(["value" => $surface]);

        if($words) throw new \Exception("Similar word exist for " . $words[0]->getValue() . ": ID " . $words[0]->getId());


         $baseId = $em->getRepository(Base::class)->createQueryBuilder("b")
            ->select("max(b.id)")
            ->getQuery()
            ->getSingleScalarResult();

        $baseId = max(1000000, $baseId + 1);
        $base = new Base();
        $base->setId($baseId);
        $base->setValue($surface);
        $em->persist($base);

        $word = new Word();
        $word->setBase($base);
        $word->setType("kanji");
        $word->setValue($surface);
        

        $text = new Text();
        $kanjis = array_unique($text->splitKanjis($word->getValue()));
                
        foreach($kanjis as $kanji) {

            $heisig = $em->getRepository(Heisig::class)->findOneByKanji($kanji);

            
            $wordKanji = new WordKanji();
            $wordKanji->setHeisig($heisig);
            $wordKanji->setWord($word);
            $wordKanji->setKanji($kanji);

            $em->persist($wordKanji);

        }

        $word->setHasWordKanjis(count($kanjis) ? true : false);
        $word->setNoKanji(count($kanjis) ? false : true);

        $em->persist($word);

        $wordReading = new WordReading();
        $wordReading->setWord($word);

        if($surface != $hiragana) {

            $readingWord = new Word();
            $readingWord->setBase($base);
            $readingWord->setType("reading");
            $readingWord->setValue($hiragana);
            $readingWord->setNoKanji(1);
            $readingWord->setHasWordKanjis(0);
            $em->persist($readingWord);

            $wordReading2 = new WordReading();
            $wordReading2->setWord($readingWord);
            $wordReading2->setReading($readingWord);
            $wordReading2->setCode(5);
            $em->persist($wordReading2);

        }
        else {

            $readingWord = $word;

        }


        $wordReading->setReading($readingWord);
        $wordReading->setCode(5);
        $em->persist($wordReading);


        $sense = new Sense();
        $sense->setBase($base);
        $em->persist($sense);

        $sensePartOfSpeech = new SensePartOfSpeech();
        $sensePartOfSpeech->setSense($sense);
        $sensePartOfSpeech->setPartOfSpeech($partOfSpeech);

        $senseWord = new SenseWord();
        $senseWord->setSense($sense);
        $senseWord->setWord($word);

        $em->persist($senseWord);

        $senseWord = new SenseWord();
        $senseWord->setSense($sense);
        $senseWord->setWord($readingWord);

        $em->persist($senseWord);

        $senseGlossary = new SenseGlossary();
        $senseGlossary->setSense($sense);
        $senseGlossary->setGlossary($glossary);

        $em->persist($senseGlossary);

        return $wordReading;

    }

}

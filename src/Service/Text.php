<?php 

namespace Maalls\JMDictBundle\Service;


class Text {

    private $em;

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em)
    {

        $this->em = $em;

    }

    public function parse($q) 
    {

        $em = $this->em;
        $map = [
            "動詞" => "verb",
            "助動詞" => "auxiliary verb",
            "助詞" => "particule",
            "接頭詞" => "prefix",
            "記号" => "symbol",
            "名詞" => "noun",
            "感動詞" => "interjection"
        ];

        $subMap = [
            "固有名詞" => "proper noun",
            "副詞可能" => "adverb",
            "代名詞" => "pronoun",
            "接尾" => "suffix"
        ];

        $sentences = [];

        if($q) {

            $ss = explode("。", $q);
            
            foreach($ss as $s) {

                $parts = [];
                $mecab = new \MeCab\Tagger();
                $nodes = $mecab->parseToNode($s);

                foreach($nodes as $node) {

                    $part = ["surface" => $node->getSurface()];

                    $features = explode(",", $node->getFeature());
                    $part["partOfSpeech"] = isset($map[$features[0]]) ? $map[$features[0]] : $features[0];
                    $part["subClass"] = isset($subMap[$features[1]]) ? $subMap[$features[1]] : $features[1];
                    if(
                        isset($features[6]) && 
                        !in_array($features[0], ["助動詞", "助詞", "BOS/EOS", "接頭詞", "記号"]) &&
                        !in_array($features[1], ["固有名詞", "接尾"])

                    ) {

                        $words = $em->getRepository(\Maalls\JMDictBundle\Entity\Word::class)->findBy(["value" => $features[6]]);

                        if($words) {

                            $word = $words[0];

                            $kanjiReadings = $word->getKanjiReadings();

                            if(count($kanjiReadings) > 0) {

                                $reading = $kanjiReadings[0]->getReading();
                                $part["reading"] = $reading->getValue();

                            }
                            else {

                                $part["reading"] = '';

                            }
                            $part["word"] = $word;
                            $senseWords = $word->getSenseWords();
                            $part["glossaries"] = $senseWords[0]->getSense()->getSenseGlossaries();
                            //$part["words"] = $words;

                            
                            $kanjis = $em->getRepository(\Maalls\HeisigBundle\Entity\Heisig::class)->findBySentence($word->getValue());

                            $part["kanjis"] = $kanjis;

                            $parts[] = $part;
                        }

                    }

                }

                $sentences[] = ["sentence" => $s, "parts" => $parts];

            }

        }

        return $sentences;

    }

}
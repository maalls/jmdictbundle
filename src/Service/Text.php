<?php 

namespace Maalls\JMDictBundle\Service;

/*
0.表層 -> surface
1.形品詞 -> pos
2.品詞細分類1 -> pos class. 1
3.品詞細分類2 -> pos class. 2
4.品詞細分類3 -> pos class. 3
5.活用型 -> useful type
6.活用形 -> utilization form
7.原形 -> oroginal form
8.読み -> read
9.発音 -> pronounciation

http://taku910.github.io/mecab/
*/

class Text {

    private $em;

    public function __construct(\Doctrine\Common\Persistence\ObjectManager $em)
    {

        $this->em = $em;

    }

    public function tokenize($text)
    {

        $em = $this->em;
        $mecab = new \MeCab\Tagger();
        $nodes = $mecab->parseToNode($text);

        $tokens = [];

        foreach($nodes as $node) 
        {

            if(!$node->getSurface()) continue;
            $token = ["surface" => "", "word_reading" => null, "kanjis" => null, "furigana" => null, "features" => null];;
            $token["surface"] = $node->getSurface();

            $features = explode(",", $node->getFeature());

            $token["pos"] = $features[0];
            $token["features"] = $features;
            
            $token["furigana"] =isset($features[7]) && $features[7] != $node->getSurface() && mb_convert_kana($features[7], "cH") !=  $node->getSurface() ? mb_convert_kana($features[7], "cH"):'';
            
            if($features[6] && $features[6] != "*") {

                // if furigana is available, use it to target the proper word.

                $wordReading = $em->getRepository(\Maalls\JMDictBundle\Entity\WordReading::class)->createQueryBuilder("wr")
                    ->join("wr.word", "w")
                    ->join("wr.reading", "r")
                    ->where("w.value = :word and r.value = :furigana")
                    ->setParameters(["word" => $features[6], "furigana" => $token["furigana"]])
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();

                /*$wordReading = $em->getRepository(\Maalls\JMDictBundle\Entity\WordReading::class)->createQueryBuilder("w")
                    ->join("w.base", "b")
                    ->join("b.words", "ws")
                    ->where("w.value = :word and ws.value = :furigana")
                    ->setParameters(["word" => $features[6], "furigana" => $token["furigana"]])
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();*/

                if(!$wordReading) {

                    
                    $wordReading = $em->getRepository(\Maalls\JMDictBundle\Entity\WordReading::class)
                        ->createQueryBuilder("wr")
                        ->join("wr.word", "w")
                        ->where("w.value = :value")->setParameter("value",  $features[6])
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
                        

                }

                if($wordReading) {

                    $glossaries = [];
                    

                    $token["word_reading"] = ["id" => $wordReading->getId()];

                    $kanjis = $em->getRepository(\Maalls\HeisigBundle\Entity\Heisig::class)->findBySentence($wordReading->getWord()->getValue());
                    foreach($kanjis as $kanji) {

                        $token["kanjis"][] = [
                            "kanji" => $kanji->getKanji(), 
                            "keyword" => $kanji->getKeyword(),
                            "constituent" => $kanji->getConstituent()
                        ];

                    }

                }

            }

            $tokens[] = $token;


        }

        return $tokens;

    }

/*
    public function parse($q, $wordOnly = true) 
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
                    $word = null;

                    if(
                        isset($features[6]) && 
                        !in_array($features[0], ["助動詞", "助詞", "BOS/EOS", "接頭詞", "記号"]) &&
                        !in_array($features[1], ["固有名詞", "接尾"])

                    ) {

                        $words = $em->getRepository(\Maalls\JMDictBundle\Entity\Word::class)->findBy(["value" => $features[6]]);

                        if($words) {

                            $word = $words[0];
                            $part["word"] = $word;
                            $kanjis = $em->getRepository(\Maalls\HeisigBundle\Entity\Heisig::class)->findBySentence($word->getValue());
                            $part["kanjis"] = $kanjis;

                            $parts[] = $part;
                        }

                    }

                    if(!$wordOnly && !$word) {

                        $parts[] = $part;

                    }

                }

                $sentences[] = ["sentence" => $s, "parts" => $parts];

            }

        }

        return $sentences;

    }
    */

}
<?php 

namespace Maalls\JMDictBundle\Service;


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
        
        preg_match_all("/(.)/ius", $text, $match);
        $original_chars = isset($match[0]) ? $match[0] : [];

        $tokens = [];

        $texts = explode("\n", $text);
    

        foreach($texts as $text) {

            $nodes = $mecab->parseToNode($text);

            foreach($nodes as $k => $node) 
            {

                if(!$node->getSurface()) {

                    continue;

                }
                $token = ["surface" => "", "word" => null, "kanjis" => null, "furigana" => null, "features" => null];;
                $token["surface"] = $node->getSurface();

                $features = explode(",", $node->getFeature());

                $token["pos"] = $features[0];
                $token["features"] = $features;
                
                $token["furigana"] =isset($features[7]) && $features[7] != $node->getSurface() && mb_convert_kana($features[7], "cH") !=  $node->getSurface() ? mb_convert_kana($features[7], "cH"):'';
                if($features[6] && $features[6] != "*") {
                
                    $word = $em->getRepository(\Maalls\JMDictBundle\Entity\Word::class)->findOneBy(["value" => $features[6]]);

                    if($word) {

                        $glossaries = [];
                        foreach($word->getGlossaries() as $glossary) {

                            $glossaries[] = (string)$glossary;

                        }

                        $token["word"] = [
                            "id" => $word->getId(),
                            "value" => $word->getValue(), 
                            "reading" => (string)$word->getReading(),
                            "glossaries" => implode(", ", $glossaries)

                        ];

                        $kanjis = $em->getRepository(\Maalls\HeisigBundle\Entity\Heisig::class)->findBySentence($word->getValue());
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

            $tokens[] = ["surface" => "\n", "word" => null, "kanjis" => null, "furigana" => null, "features" => ["EOL"]];

        }

        // remove triming line break
        array_pop($tokens);

        $finalTokens = [];

        $index = 0;
        foreach($tokens as $token) {

            $surface = $token["surface"];
            if(!$surface && $token["features"] == "EOL") {

                $chars = ["\n"];

            }
            else {

                preg_match_all("/./ius", $surface, $match_token);

                $chars = $match_token[0];

            }

            foreach($chars as $char) {

                do {

                    $original_char = $original_chars[$index];

                    if($original_char != $char) {

                        $finalTokens[] = ["surface" => $original_char, "word" => null, "kanjis" => null, "furigana" => null, "features" => ["SPC"]];;
                        //echo "- => $original_char" . "<br/>";
                    }
                    else {

                        //echo "$char => $original_char" . "<br/>";

                    }
                    $index++;


                }
                while($original_char != $char);

            }

            $finalTokens[] = $token;

        }
        
        return $finalTokens;

    }


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

}
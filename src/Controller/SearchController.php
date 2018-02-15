<?php

namespace Maalls\JMDictBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller
{

     /**
      * @Route("/search", name="search")
      */
    public function index(Request $request, \Doctrine\Common\Persistence\ObjectManager $em)
    {

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

        $q = $request->request->get("q");
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
                            $part["words"] = $words;
                            $parts[] = $part;
                        }

                    }

                }

                $sentences[] = ["sentence" => $s, "parts" => $parts];

            }

        }

        return $this->render('@JMDict/Search/index.html.twig', ["q" => $q, "sentences" => $sentences]);

    }
}
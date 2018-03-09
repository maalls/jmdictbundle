<?php

namespace Maalls\JMDictBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/", name="maalls_jmdict_")
 */

class SearchController extends Controller
{

     /**
      * @Route("/search", name="search")
      */
    public function index(Request $request, \Maalls\JMDictBundle\Service\Text $textService)
    {

        $q = $request->request->get("q");
        $sentences = $textService->parse($q);

        return $this->render('@JMDict/Search/index.html.twig', ["q" => $q, "sentences" => $sentences]);

    }
}
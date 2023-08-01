<?php

namespace App\Controller;

use Pimcore\Bundle\AdminBundle\Controller\Admin\LoginController;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\Type\SearchType;
use Pimcore\Model\DataObject\Movie;



class SearchController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/search", name="search_movie", methods={"GET"})
     */
    public function defaultAction(Request $request, ApplicationLogger $logger): Response
    {
            $query = trim($request->get("query"));
            $form = $this->createForm(SearchType::class);
            $form->handleRequest($request);
            $moviesListing = new Movie\Listing();
            $moviesListing->setCondition("movieTitle LIKE ?",["%$query%"]);
            $moviesListing->setLimit(21);
            return $this->render("search/results.html.twig",["query"=>$query, "movies"=>$moviesListing]);
    }
}

<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\Movie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\Type\MovieType;
use App\Entity\MovieEntity;

use App\Service\APIService;

class JsonController extends FrontendController
{
    public function defaultAction(Request $request,APIService $apiConn): Response
    {
        return $this->render("api/view.html.twig");
    }
    /**
     * @param Request $request
     * @return Response
     * @Route("/api/movie/{id}", name="detail_movie_api", methods={"GET"})
     */
    public function getMovieAction(Request $request, ApplicationLogger $logger, $id): JsonResponse
    {
        $movie = Movie::getById($id);
        $jsonMovie = $this->jsonifyMovieDataObject($movie);
        $logger->info("converted movie dataobject with id $id to json and served via API");
        return $this->json($jsonMovie);
    }

    private function jsonifyMovieDataObject(Movie $movie)
    {
        $description = $movie->getDescription();
        $title = $movie->getMovieTitle();
        $movieID = $movie->getId();
        $imageLink = $movie->getBackdrop()->getUrl();
        $jsonMovie = array("id" => $movieID, "title" => $title,"imageURL"=>$imageLink, "description" => $description);
        return $jsonMovie;
    }
    /**
     * @param Request $request
     * @return Response
     * @Route("/api/movies", name="all_movies", methods={"GET"})
     */
    public function getAllMoviesAction(Request $request, ApplicationLogger $logger): JsonResponse
    {
        $jsonMovieArray  = array();
        $movies = new Movie\Listing();
        $id = 0;
        foreach ($movies as $movie) {
            $jsonifiedMovie = $this->jsonifyMovieDataObject($movie);
            $jsonMovieArray[$id] = $jsonifiedMovie;
            $id++;
        }

        return $this->json($jsonMovieArray);
    }

     /**
     * @param Request $request
     * @return Response
     * @Route("/api/create/{movieName}", name="create", methods={"GET"})
     */
    public function createMovieAction($movieName, ApplicationLogger $applicationLogger): JsonResponse
    {
        $res = array();
        $movie = $res["results"][0];
        $movieDataObject = new Movie();
        $movieDataObject->setMovieTitle($movie["original_title"]);
        $movieDataObject->setDescription($movie["overview"]);
        $movieDataObject->setId($movie["id"]);
        $movieDataObject->setParentId(1);
        $movieDataObject->setKey($movie["title"]);
        $movieDataObject->setPublished(true);
        $movieDataObject->save();
        $applicationLogger->debug("Movie added");
        return $this->json($movie);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/success_add", name="movie_add_success", methods={"GET"})
     */
    public function movieAddSuccess() :Response {
        return $this->render("success/view.html.twig");

    }

}

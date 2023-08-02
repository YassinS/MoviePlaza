<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class APIService
{
    protected $bearerTokenTMDB;

    public function __construct(){
            $this->bearerTokenTMDB = $_ENV["API_BEARER_TOKEN_TMDB"];
    }

    public function getMovieDetailsByName($movieName){
        $client = new Client();
        $bearerTokenTMDB = $this->bearerTokenTMDB;
        $promise = $client->requestAsync('GET', "https://api.themoviedb.org/3/search/movie?query=$movieName", [
            'headers' => [
            'Authorization' => "Bearer $bearerTokenTMDB",
            'accept' => 'application/json',
            ],
        ]);
       try {
                $response = $promise->wait();

                return $response->getBody()->getContents();
     } catch (RequestException $e) {
                dd($promise);
    }
    }

    public function getMovieDetailsById($movieId){
        $client = new Client();
        $bearerTokenTMDB = $this->bearerTokenTMDB;
        $promise = $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/$movieId", [
            'headers' => [
            'Authorization' => "Bearer $bearerTokenTMDB",
            'accept' => 'application/json',
            ],
        ]);
       try {
                $response = $promise->wait();
                // Here, $response is the actual Response object, not a Promise.
                // You can now get the data from the response and return it.

                return $response->getBody()->getContents();
     } catch (RequestException $e) {
                // Handle the error if the promise is rejected (e.g., network issue, API error).
                dd($promise);
    }
    }


    public function getTopRatedMovies($page){
        $client = new Client();
        $bearerTokenTMDB = $this->bearerTokenTMDB;
        $promise = $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/top_rated?page=$page", [
            'headers' => [
            'Authorization' => "Bearer $bearerTokenTMDB",
            'accept' => 'application/json',
            ],
            ]);
        try {
                $response = $promise->wait();
                return $response->getBody()->getContents();
        } catch (RequestException $e) {
                dd($promise);
        }

    }

    public function getMovieDetail($movieID){
            $client = new Client();
        $bearerTokenTMDB = $this->bearerTokenTMDB;
        $promise = $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/$movieID", [
            'headers' => [
            'Authorization' => "Bearer $bearerTokenTMDB",
            'accept' => 'application/json',
            ],
            ]);
        try {
                $response = $promise->wait();
                return $response->getBody()->getContents();
        } catch (RequestException $e) {
                dd($promise);
        }

    }
}

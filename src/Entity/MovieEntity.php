<?php

namespace App\Entity;

use Exception;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\Movie;

class MovieEntity{

    private $movieDao;

    function __construct()
    {
        $this->movieDao = new Movie();
        $this->movieDao->setParentId(1);
        $this->movieDao->setPublished(true);

    }

    public function setTitle($title){
        $this->movieDao->setMovieTitle($title);
    }

    public function setDescription($description) {
        $this->movieDao->setDescription($description);
    }
    public function setId($id) {
        $this->movieDao->setId($id);
    }

    public function setKey() {
        $this->movieDao->setKey(\Pimcore\Model\Element\Service::getValidKey($this->getTitle(), 'object'));
    }

    public function setBackdrop(string $backdropURL) {
        $img = new ExternalImage($backdropURL);
        $this->movieDao->setBackdrop($img);
    }

    public function setBudget($budget) {
        $this->movieDao->setBudget($budget);
    }

    public function getBudget() {
        return $this->movieDao->getBudget();
    }

    public function getTitle() {
        try{
        return $this->movieDao->getMovieTitle();
        } catch(Exception){
                return "Set title before setting key";
        }
    }

    public function getDescription() {
        return $this->movieDao->getDescription();
    }

    public function getId() {
        return $this->movieDao->getId();
    }

    public function getBackdrop() {
        return $this->movieDao->getBackdrop();
    }

    public function save() {
        $id = $this->movieDao->getId();
        $title = $this->getTitle();
        $this->movieDao->save(["versionNote"=>"Movie with id: $id and title: $title create automatically"]);

    }

}

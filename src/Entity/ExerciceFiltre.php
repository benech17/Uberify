<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class ExerciceFiltre{
    
   /**
     * @var Category
     */
    private $category;

    /**
     * @var int|null
     * @Assert\Range(min=0,max=5)
     */
    private $minDifficulte;

    /**
     * @var int
     */
    private $appreciation;
    

    
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @return ExerciceFiltre
     */
    public function setCategory(?Category $category){
       $this->category =$category;
       return $this;
    }




    /**
     * @return int|null
     */
    public function getMinDifficulte(){
        return $this->minDifficulte;
    }

     /**
     * @return ExerciceFiltre
     */
    public function setMinDifficulte(int $difficulte){
        $this->minDifficulte =$difficulte;
        return $this;
     }

     /**
     * @return int|null
     */
    public function getAppreciation(){
        return $this->appreciation;
    }

     /**
     * @return ExerciceFiltre
     */
    public function setAppreciation(int $appreciation){
        $this->appreciation =$appreciation;
        return $this;
     }
}
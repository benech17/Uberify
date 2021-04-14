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
}
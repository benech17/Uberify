<?php

namespace App\Controller ;

use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{

    /**
     * @Route("/",name="home")
     * @return Response
     */
    public function index (FormationRepository $repository) :Response{
        $formations = $repository->findLatest();
        return $this->render('pages/home.html.twig',[
            'formations'=>$formations
        ]);
    }

   

}
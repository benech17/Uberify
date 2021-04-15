<?php

namespace App\Controller ;

use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController{

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

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

    /**
     * @Route("/trouverUnEnseignant", name="usersEnseignants.index")
     */
    public function indexEnseignants()
    {
        $users = $this->repository->findAllByNote();
        return $this->render('enseignant/index.html.twig', [
            'users' => $users
        ]);
    }


}

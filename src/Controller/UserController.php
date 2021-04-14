<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Exercice;
use App\Entity\Resolution;
use App\Repository\UserRepository;
use App\Repository\ExerciceRepository;
use App\Repository\ResolutionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Async\ResolveCache;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    /**
     * @var ExerciceRepository
     */
    private $repository_exercices;

    /**
     * @var UserRepository
     */
    private $repository_users;

    /**
     * @var ResolutionRepository
     */
    private $repository_resolutions;

    public function __construct(ExerciceRepository $repository_exercices, EntityManagerInterface $manager, Security $security, UserRepository $repository_users, ResolutionRepository $repository_resolutions)
    {
        $this->repository_exercices = $repository_exercices;
        $this->manager = $manager;
        $this->security = $security;
        $this->repository_users = $repository_users;
        $this->repository_resolutions = $repository_resolutions;
    }



    /**
     * @Route("/user-{id}" , name="user.show")
     * @return Response
     */
    public function show($id, Security $security): Response
    {
        if ($security->getUser()) {
            $repository_user = $this->repository_users;
            $repository_exercices = $this->repository_exercices;
            $repository_resolution = $this->repository_resolutions;

            $user = $repository_user->find($id);
            $exercices_enseignant = $repository_exercices->findAllCreated($id);
            $resolution_etudiant = $repository_resolution->findAllExerciceByUser($user); // Resolution[]

            if (in_array("ROLE_ENSEIGNANT", $user->getRoles())) {
                return $this->render('user/show_enseignant.html.twig', [
                    'user' => $user,
                    'exercices' => $exercices_enseignant,
                    'current_menu' => 'user'
                ]);
            } else {
                return $this->render('user/show_etudiant.html.twig', [
                    'user' => $user,
                    'resolutions' => $resolution_etudiant,
                    'current_menu' => 'user'

                ]);
            }
        } else {
            $this->addFlash('mustLogin', 'Vous devez vous connecter pour avoir accès à cette partie');
            return $this->redirectToRoute('login');
        }
    }
}

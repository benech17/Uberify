<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FormationController extends AbstractController
{

    public function __construct(FormationRepository $repository, EntityManagerInterface $manager, Security $security)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->security = $security;
    }


    /**
     * @Route("/formations/{slug}-{id}",name="formation.show",requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show($slug, $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Formation::class);
        $formation = $repository->find($id);
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,

        ]);

        return $this->redirectToRoute('formation.show', [
            'id' => $id,
            'slug' => $slug
        ]);
    }

    /**
     * @Route("/formations/{slug}-{id}/inscription",name="formation.inscription",requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function inscription($slug, $id, EntityManagerInterface $em,Security $security)
    {
        if ($security->getUser()) {
            $repository = $this->getDoctrine()->getRepository(Formation::class);
            $formation = $repository->find($id);
            $current_user = $this->security->getUser();

            $formation->addInscrit($current_user);
            $em->persist($formation);
            $em->flush();

            return $this->render('formation/show.html.twig', [
                'formation' => $formation
            ]);
            return $this->redirectToRoute('formation.show', [
                'id' => $id,
                'slug' => $slug
            ]);
        } else {
            $this->addFlash('mustLogin', 'Vous devez vous connecter pour avoir accès à cette partie');
            return $this->redirectToRoute('login');
        }
    }
}

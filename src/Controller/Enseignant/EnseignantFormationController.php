<?php

namespace App\Controller\Enseignant;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\ExerciceRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class EnseignantFormationController extends AbstractController
{
    /**
     * @var FormationRepository
     */
    private $repository;

    /**
     * @var ExerciceRepository
     */
    private $repository_exercices;


    public function __construct(FormationRepository $repository, ExerciceRepository $repository_exercices, EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->security = $security;
        $this->repository_exercices = $repository_exercices;
    }

    /**
     * @Route("/enseignant/formations",name="enseignant.formation.index")
     */
    public function index(): Response
    {
        $user = $this->security->getUser();
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {            //affiche all to admin
            $formations = $this->repository->findAll();                  
        } else {
            $formations = $this->repository->findAllCreated($user->getId());   //affiche only their to teacher
        }
        return $this->render('enseignant/formation/index.html.twig', [
            'formations' => $formations,
            'current_menu' => 'enseignant'
        ]);
    }

    /**
     * @Route("/enseignant/formations/create",name="enseignant.formation.new")
     */
    public function new(Request $request)
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);  //gere la requete
        if ($form->isSubmitted() && $form->isValid()) {
            $formation->setAuthor($this->security->getUser());

            $this->manager->persist($formation);
            foreach ($formation->getExercices() as $exercice_selected) {
                $exercice_selected->setFormation($formation);
                $this->manager->persist($exercice_selected);
            }

            $this->manager->flush();
            $this->addFlash('enseignant-formation', 'Bien créé avec succés');
            return $this->redirectToRoute('enseignant.formation.index'); //listing des exos
        }
        return $this->render('enseignant/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/enseignant/formations/{id}",name="enseignant.formation.edit",methods="GET|POST")
     */
    public function edit(Formation $formation, Request $request)
    {

        $form = $this->createForm(formationType::class, $formation);
        $form->handleRequest($request);  //gere la requete
        $exercice_list = $this->repository_exercices->findAllCreated($this->security->getUser()->getId());
        if ($form->isSubmitted() && $form->isValid()) {
            $formation->setAuthor($this->security->getUser());
            foreach ($formation->getExercices() as $exercice_selected) {
                $exercice_selected->setFormation($formation);
                $this->manager->persist($exercice_selected);
            }

            foreach ($exercice_list as $exercice) {
                if (!in_array($exercice, $formation->getExercices()->toArray()) and $exercice->getFormation() != null and $exercice->getFormation()->getId() == $formation->getId()) {
                    $exercice->setFormation(null);
                }
            }

            $this->manager->flush();
            $this->addFlash('enseignant-formation', 'Bien modifié avec succés');
            return $this->redirectToRoute('enseignant.formation.index'); //listing des exos
        }

        return $this->render('enseignant/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/enseignant/formation/{id}",name="enseignant.formation.delete",methods="DELETE")
     */
    public function delete(Formation $formation, Request $request)
    {

        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->get('_token'))) {
            $this->manager->remove($formation);
            $this->manager->flush();
            $this->addFlash('enseignant-formation', 'Bien supprimé avec succés');
        }

        return $this->redirectToRoute('enseignant.formation.index'); //listing des exos
    }
}

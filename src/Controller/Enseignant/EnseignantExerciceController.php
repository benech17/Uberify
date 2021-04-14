<?php

namespace App\Controller\Enseignant;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class EnseignantExerciceController extends AbstractController
{
    /**
     * @var ExerciceRepository
     */
    private $repository;

    public function __construct(ExerciceRepository $repository, EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->security = $security;
    }

    /**
     * @Route("/enseignant/exercices",name="enseignant.exercice.index")
     */
    public function index(): Response
    {
        $user = $this->security->getUser();
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            $exercices = $this->repository->findAll();                           //affiche all only  to admin
        } else {
            $exercices = $this->repository->findAllCreated($user->getId());     //affiche only their to teacher
        }
        return $this->render('enseignant/exercice/index.html.twig', [
            'exercices' => $exercices,
            'current_menu' => 'enseignant'
        ]);
    }


    /**
     * @Route("/enseignant/exercice/create",name="enseignant.exercice.new")
     */
    public function new(Request $request)
    {
        $exercice = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);  //gere la requete
        if ($form->isSubmitted() && $form->isValid()) {
            $exercice->setAuthor($this->security->getUser());

            $this->manager->persist($exercice);
            $this->manager->flush();
            $this->addFlash('enseignant-exercice', 'Bien créé avec succés');
            return $this->redirectToRoute('enseignant.exercice.index'); //listing des exos
        }
        return $this->render('enseignant/exercice/new.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/enseignant/exercice/{id}",name="enseignant.exercice.edit",methods="GET|POST")
     */
    public function edit(Exercice $exercice, Request $request, CacheManager $cache, UploaderHelper $helper)
    {

        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);  //gere la requete

        if ($form->isSubmitted() && $form->isValid()) {
            $exercice->setAuthor($this->security->getUser());

            //supprime les anciennes images , stockés dans le cache media/
            if ($exercice->getSolutionFile() instanceof UploadedFile) {
                $cache->remove($helper->asset($exercice, 'solutionFile'));
            }
            $this->manager->flush();
            $this->addFlash('enseignant-exercice', 'Bien modifié avec succés');
            return $this->redirectToRoute('enseignant.exercice.index'); //listing des exos
        }

        return $this->render('enseignant/exercice/edit.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/enseignant/exercice/{id}",name="enseignant.exercice.delete",methods="DELETE")
     */
    public function delete(Exercice $exercice, Request $request, CacheManager $cache, UploaderHelper $helper)
    {
        //supprime les anciennes images , stockés dans le cache media/
        if ($exercice->getSolutionFile() instanceof UploadedFile) {
            $cache->remove($helper->asset($exercice, 'solutionFile'));
        }

        if ($this->isCsrfTokenValid('delete' . $exercice->getId(), $request->get('_token'))) {
            $this->manager->remove($exercice);
            $this->manager->flush();
            $this->addFlash('enseignant-exercice', 'Bien supprimé avec succés');
        }

        return $this->redirectToRoute('enseignant.exercice.index'); //listing des exos
    }
}

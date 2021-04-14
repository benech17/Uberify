<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin", name="admin.")
 */
class AdminController extends AbstractController
{
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
     * @Route("/", name="users.index")
     */
    public function index()
    {
        $users = $this->repository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'current_menu' => 'admin'
        ]);
    }


    /**
     * @Route("/users/edit/{id}",name="users.edit")
     */
    public function editUser(User $user, Request $request)
    {

        $form = $this->createForm(EditUserType::class, $user,[
            'validation_groups' => ['update']
        ]);
        $form->handleRequest($request);  //gere la requete

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('user-edit-delete', 'Bien modifié avec succés');
            return $this->redirectToRoute('admin.users.index'); //listing des exos
        }
    
        return $this->render('admin/edit.html.twig', [
            'user'=>$user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/users/{id}",name="users.delete",methods="DELETE")
     */
    public function delete(User $user, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))) {
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('user-edit-delete', 'Bien supprimé avec succés');
        }

        return $this->redirectToRoute('admin.users.index'); //listing des users
    }
}
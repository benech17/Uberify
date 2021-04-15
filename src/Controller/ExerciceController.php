<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Exercice;
use App\Form\CommentType;
use App\Entity\Resolution;
use App\Entity\ExerciceFiltre;
use Doctrine\ORM\EntityManager;
use App\Form\ExerciceFiltreType;
use App\Repository\ExerciceRepository;
use App\Repository\ResolutionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExerciceController extends AbstractController
{
    /**
     * @var ExerciceRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Security
     */
    private $security;

    /**
     *
     * @var ResolutionRepository
     */
    private $repo_res;

    public function __construct(ExerciceRepository $repository, EntityManagerInterface $manager, Security $security,ResolutionRepository $repo_res)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->security = $security;
        $this->repo_res= $repo_res;
    }


    /**
     * @Route("/exercices" , name="exercice.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new ExerciceFiltre();
        $form = $this->createForm(ExerciceFiltreType::class, $search);
        $form->handleRequest($request);

        $exercices = $paginator->paginate(
            $this->repository->findAllQuery($search),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('exercice/index.html.twig', [
            'current_menu' => 'exercices',
            'exercices' => $exercices,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/exercices/{slug}-{id}",name="exercice.show",requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show($slug, $id, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $repository = $this->getDoctrine()->getRepository(Exercice::class);
        $exercice = $repository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setExercice($exercice)
                ->setAuthor($this->security->getUser()->getUsername());


            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('exercice.show', [
                'id' => $id,
                'slug' => $slug
            ]);
        }
        $exercice = $this->repository->find($id);
        $myfile = __DIR__ . '/../../public/images/exercices/' . $exercice->getFilename();
        $fn = fopen($myfile, 'r');
        while (!feof($fn)) {
            $result[] = fgets($fn);
        }
        fclose($fn);

        return $this->render('exercice/show.html.twig', [
            'solutions' => $result,
            'exercice' => $exercice,
            'current_menu' => 'exercices',
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/exercices/{slug}-{id}/play",name="exercice.play",requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function play($slug, $id, Request $request,Security $security,EntityManagerInterface $manager)
    {
        if ($this->security->getUser()) {
            $exercice = $this->repository->find($id);
            $myfile = __DIR__ . '/../../public/images/exercices/' . $exercice->getFilename();
            $fn = fopen($myfile, 'r');
            while (!feof($fn)) {
                $resultTab[] = trim(fgets($fn));
                $resultTab = array_filter($resultTab);
            }
            $copy=$resultTab;
            shuffle($resultTab);
            fclose($fn);

            $user=$security->getUser();
            $repository_resolution=$this->repo_res;
            if ($request->isXMLHttpRequest()) {

                $resolutions=$repository_resolution->findByUserAndExercice($exercice,$user);
                if(empty($resolutions) or $resolutions == null){
                    $resolution= new Resolution();
                }else{
                    $resolution=$resolutions[0];
                }
                $resolution->setExercice($exercice)
                    ->setUser($user)
                    ->setLastTryAt(new \DateTime());
                if($resolution->getIsResolved()==false){
                    $resolution->setTentatives(($resolution->getTentatives()+1));
                }
                $indexArray = $_POST["indexArray"];
                if ($copy == $indexArray) {
                    $result = "s";
                    $resolution->setIsResolved(true);
                } else {
                    $result = "f";
                }
                $manager->persist($resolution);
                $manager->flush();

                $reponse = new JsonResponse();
                $reponse->headers->set('Content-Type', 'application/json');
                json_encode(["result" => $result]);
                $reponse->setData(json_encode(["result" => $result]));
                return $reponse;
            }
            $repository_res=$this->repo_res->findByUserAndExercice($exercice,$this->security->getUser());

            return $this->render('exercice/probleme.html.twig', [
                'solutions' => $resultTab,
                'exercice' => $exercice,
                'resolution_rep'=>$repository_res
            ]);
        } else {
            $this->addFlash('mustLogin', 'Vous devez vous connecter pour avoir accès à cette partie');
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/exercices/{id}/vote/{feedback}",name="exercice.feedback",requirements={"slug": "[a-z0-9\-]*", "feedback" : "\d+"})
     * @return Response
     */
    public function voter($id, $feedback, Request $request, Security $security, EntityManagerInterface $manager)
    {
        $reponse = new JsonResponse();
        $reponse->headers->set('Content-Type', 'application/json');
        $result = "f";

        if ($user = $this->security->getUser()) {
            $exercice = $this->repository->find($id);
            $security->getUser();

            if ($feedback >= 0 && $feedback <= 5) {
                $exercice->updateMeanFeedback($feedback);
                $exercice->getUser()->updateMeanNote($feedback);
                $manager->persist($exercice);
                $manager->flush();

                $result = "s";

                json_encode(["result" => $result]);
                $reponse->setData(json_encode(["result" => $result]));
                return $reponse;
            }
        }

        json_encode(["result" => $result]);
        $reponse->setData(json_encode(["result" => $result]));
        return $reponse;
    }
}

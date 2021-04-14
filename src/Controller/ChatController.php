<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Resolution;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManager;
use App\Form\ExerciceFiltreType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;


class ChatController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var ConversationRepository
     */
    private $convRepository;

    /**
     * @var MessageRepository
     */
    private $msgRepository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var SerializerInterface
     */
    private $serializer;


    public function __construct(Security $security, ConversationRepository $convRepository, MessageRepository $msgRepository, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $this->security = $security;
        $this->convRepository = $convRepository;
        $this->msgRepository = $msgRepository;
        $this->manager = $manager;
    }


    /**
     * @Route("/conversations" , name="conversation.list")
     * @return Response
     */
    public function listerConversations(UserInterface $user, Request $request): Response
    {
        $conversations = $this->convRepository->findConversationsByUser($user->getId());

        //return $this->render('chat/index.html.twig', [
            //'conversation' => $conversations,
        //]);
    }

    /**
     * @Route("/conversation/{convId}/{lastMsgId}",name="conversation.show",requirements={"convId": "d+", "lastMsgId": "d+"})
     * @return Response
     */
    public function showMessages($convId, $lastMsgId, Request $request): Response
    {
        $user = $this->security->getUser();
        $conversation = $this->convRepository->find($convId);
        if($conversation == null || ($conversation->getUserOne()->getId() != $user->getId()
                && $conversation->getUserTwo()->getId() != $user->getId()))
        {
            return $this->redirectToRoute('/', [], 301);
        }

        $messages = $this->msgRepository->findMessagesByConversation($lastMsgId, $convId);

        return $this->render('chat/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Put("/conversation/{convId}/{lastMsgId}",name="conversation.show")
     * @return Response
     *
     * @View
     */
    public function sendMessage($convId, $lastMsgId, Request $request): Response
    {
        $user = $this->security->getUser();
        $data = (array)(json_decode($request->getContent()));

        $message = new Message();
        $message->setCreatedAt(new \DateTime());
        $form = $this->get('form.factory')->create(MessageType::class, $message);
        $form->submit($data);

        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        if($form->isValid()) {
            $response->setContent(json_encode(""));
        }else {
            var_dump($form->getErrors());
            $response->setContent(json_encode($form->getErrors()));
        }
        return $response;
        return $this->render('chat/index.html.twig', [
            'messages' => $messages,
        ]);
    }

}

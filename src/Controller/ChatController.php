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
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View;
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
    public function listerConversations(Request $request): Response
    {
        $user = $this->security->getUser();
        if($user == null)
        {
            return $this->redirectToRoute('/', [], 301);
        }

        $conversations = $this->convRepository->findConversationsByUser($user->getId());

        $convs = [];
        foreach($conversations as $conversation) {
            $conv = [ "id" => $conversation->getId(), "user" => (($conversation->getUserOne()->getId() != $user->getId())? $conversation->getUserOne() : $conversation->getUserTwo()) ];
            array_push($convs, $conv);
        }
        return $this->render('chat/index.html.twig', [
            'conversations' => $convs,
        ]);
    }

    /**
     * @Route("/conversation/{convId}/{lastMsgId}",name="conversation.showMessages",requirements={"convId": "d+", "lastMsgId": "d+"})
     */
    public function showMessages($convId, $lastMsgId, Request $request)
    {
        $reponse = new JsonResponse();
        $reponse->headers->set('Content-Type', 'application/json');
        $msgs = [];

        $user = $this->security->getUser();
        $conversation = $this->convRepository->find($convId);
        if($user == null || $conversation == null || ($conversation->getUserOne()->getId() != $user->getId()
                && $conversation->getUserTwo()->getId() != $user->getId()))
        {
            return $this->redirectToRoute('/', [], 301);
        }

        $messages = $this->msgRepository->findMessagesByConversation($lastMsgId, $convId);

        foreach($messages as $message) {
            $msg = [
                "id" => $message->getId(),
                "content" => $message->getContent(),
                "createdAt" => $message->getCreatedAt(),
                "incoming" => $message->getSender() != $user,
                ];
            array_push($msgs, $msg);
        }

        $reponse->setContent(json_encode(["messages" => $msgs]));
    }

    /**
     * @Put("/conversation/{convId}",name="conversation.sendmessage")
     * @return Response
     *
     * @ViewAnnotation
     */
    public function sendMessage($convId, Request $request, EntityManagerInterface $manager): Response
    {
        $reponse = new JsonResponse();
        $reponse->headers->set('Content-Type', 'application/json');
        $result = "f";
        $newMsg = [];

        $user = $this->security->getUser();

        $conversation = $this->convRepository->find($convId);
        if($conversation == null || ($conversation->getUserOne()->getId() != $user->getId()
                && $conversation->getUserTwo()->getId() != $user->getId()))
        {
            $result = "f";
        } else {

            $data = (array)(json_decode($request->getContent()));

            $message = new Message();
            $message->setCreatedAt(new \DateTime());
            $message->setContent($data["content"]);
            $message->setSender($user);
            $message->setConversation($conversation);
            $manager->persist();
            $manager->flush();

            $result = "s";
            $newMsg = $message;
        }
        $reponse->setContent(json_encode(["result" => $result, "msg" => $newMsg]));
        return $reponse;
    }

}

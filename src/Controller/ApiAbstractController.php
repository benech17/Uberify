<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;

abstract class ApiAbstractController extends AbstractController
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}
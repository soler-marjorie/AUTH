<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    public function __construct(
        private readonly EmailService $emailService
    ){}

    #[Route('/email', name: 'email')]
    public function index(): Response
    {
        $template = $this->render('email/index.html.twig', [
            'subject'=> "Objet du message",
            'body'=> "Contenu du message"
        ]);
        $this->emailService->sendEmail('soler.marjorie81@gmail.com', 'test', '$template->getContent()');
        return new Response('Email envoyé');
    }
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MessageType;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;

class HomeController extends AbstractController
{

    private $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }
    
    #[Route('/', name: 'app_home')]
    public function index(Request $request, HubInterface $hub): Response
    {
        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $update = new Update(
                'chat',
                $this->renderView('home/message.stream.html.twig', ['message' => $form->getData()])
            );

            $hub->publish($update);

            return new Response('published!');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

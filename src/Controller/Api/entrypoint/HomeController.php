<?php

namespace App\Controller\Api\entrypoint;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }
}

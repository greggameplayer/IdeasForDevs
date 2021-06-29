<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        return $this->render('homepage/index.html.twig', [
            'locale' => strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0])
        ]);
    }
}

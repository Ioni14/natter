<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class NatterController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function home(Request $request): Response
    {
        return new Response($this->twig->render('natter.html.twig'));
    }

    /**
     * @Route("/login", methods={"GET"})
     */
    public function login(Request $request): Response
    {
        return new Response($this->twig->render('login.html.twig'));
    }
}

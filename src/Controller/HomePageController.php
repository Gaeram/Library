<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomePageController extends AbstractController

{
    #[Route('/', name: 'homepage')]
    public function homePage(){
        return $this->render('index.html.twig');
    }
}
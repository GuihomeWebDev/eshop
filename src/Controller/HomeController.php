<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function HomePage (ProductRepository $productRepository): Response
    {
        $product = $productRepository->findBy([], [], 3);
        return $this->render('home/home.html.twig',[
            'product' => $product
        ]);
    }
}

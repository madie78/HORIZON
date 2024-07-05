<?php

namespace App\Controller\Visitor\Cart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'visitor_cart_index',methods:["GET"])]
    public function index(): Response
    {
        return $this->render('pages/visitor/cart/index.html.twig');
    }
}

<?php

namespace App\Controller\Visitor\Promotion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PromotionController extends AbstractController
{
    #[Route('/promotion', name: 'visitor_promotion_index',methods:["GET"])]
    public function index(): Response
    {
        return $this->render('pages/visitor/promotion/index.html.twig');
    }
}

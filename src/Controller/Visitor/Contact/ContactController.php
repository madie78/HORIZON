<?php

namespace App\Controller\Visitor\Contact;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'visitor_contact_create',methods:["GET"])]
    public function index(): Response
    {
        return $this->render('pages/visitor/contact/create.html.twig');
    }
}

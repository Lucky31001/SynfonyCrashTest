<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractController
{
    #[Route('/catalog/item/{id}', name: 'item')]
    public function index(int $id): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
            'id' => $id,
        ]);
    }
}

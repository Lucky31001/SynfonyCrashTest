<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractController
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }
    #[Route('/catalog/item/{id}', name: 'item')]
    public function index(int $id): Response
    {

        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
            'article' => $this->articleRepository->find($id),
        ]);
    }
}

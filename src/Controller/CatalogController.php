<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\CalculService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }
    #[Route('/catalog', name: 'catalog')]
    public function index(): Response
    {
        $articles = $this->articleRepository->findAll();
        return $this->render('catalog/index.html.twig', [
            'title_page' => 'Vintud - Catalog',
            'articles' => $articles
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\CalculService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Security $security
    ) {
    }
    #[Route('/', name: 'catalog')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $articles = $this->articleRepository->findAll();
        return $this->render('catalog/index.html.twig', [
            'title_page' => 'Vintud - Catalog',
            'articles' => $articles,
            'log' => (bool)$user

        ]);
    }
}

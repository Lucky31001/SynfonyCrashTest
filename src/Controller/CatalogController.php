<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\MoneyRepository;
use App\Repository\OnSaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Security $security,
        private CategoryRepository $categoryRepository,
        private MoneyRepository $moneyRepository,
        private OnSaleRepository $onSaleRepository,
    ) {
    }
    #[Route('/', name: 'catalog')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $id = $filterForm->get('name')->getData()->getId();
            return $this->redirectToRoute('filtered_catalog', ['id' => $id]);
        }


        $user = $this->security->getUser();
        $articles = $this->articleRepository->findAll();

        $moneyAccount = 0;
        if ($user) {
            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyAccount = $money->getAccount();
        }

        $canDelete = [];
        foreach ($articles as $article) {
            $user = $this->security->getUser();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $article, 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }

        return $this->render('catalog/index.html.twig', [
            'title_page' => 'Vintud - Catalogue',
            'articles' => $articles,
            'log' => (bool)$user,
            'filter_form' => $filterForm->createView(),
            'canDelete' => $canDelete,
            'moneyAccount' => $moneyAccount,
        ]);
    }
    #[Route('filter/{id}', name: 'filtered_catalog')]
    public function filterArticle(int $id, Request $request): Response
    {
        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $id = $filterForm->get('name')->getData()->getId();
            return $this->redirectToRoute('filtered_catalog', ['id' => $id]);
        }

        $user = $this->security->getUser();
        $articles = $this->articleRepository->findBy(['category' => $id]);

        $canDelete = [];
        foreach ($articles as $article) {
            $user = $this->security->getUser();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $article, 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }

        $moneyAccount = 0;
        if ($user) {
            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyAccount = $money->getAccount();
        }

        return $this->render('catalog/filteredArticle.html.twig', [
            'title_page' => 'Vintud - Catalogue',
            'articles' => $articles,
            'log' => (bool)$user,
            'filter_form' => $filterForm->createView(),
            'canDelete' => $canDelete,
            'moneyAccount' => $moneyAccount,
        ]);
    }
}

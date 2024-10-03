<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\MoneyRepository;
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
        private MoneyRepository $moneyRepository

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

        $money = $this->moneyRepository->findOneBy(['user_id' => $user]);
        $moneyAccount = $money->getAccount();
        
        $this->moneyRepository->save($money);

        return $this->render('catalog/index.html.twig', [
            'title_page' => 'Vintud - Catalog',
            'articles' => $articles,
            'log' => (bool)$user,
            'filter_form' => $filterForm->createView(),
            'moneyAccount' => $moneyAccount
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
        return $this->render('catalog/filteredArticle.html.twig', [
            'title_page' => 'Vintud - Catalogue',
            'articles' => $articles,
            'log' => (bool)$user,
            'filter_form' => $filterForm->createView(),
        ]);
    }
}

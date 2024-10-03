<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MoneyRepository;
use App\Repository\ArticleRepository;
use App\Repository\OnSaleRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class MoneyController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository,
        private OnSaleRepository $onSaleRepository,
        private Security $security,
        private MoneyRepository $moneyRepository
    ) {
    }
    #[Route('/money/add', name: 'add_money')]
    public function index(Request $request): Response
    {
        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $id = $filterForm->get('name')->getData()->getId();
            return $this->redirectToRoute('filtered_catalog', ['id' => $id]);
        }


        $user = $this->security->getUser();
        $articles = $this->articleRepository->findAll();

        $money = $this->moneyRepository->findOneBy(['user' => $user]);
        $moneyAccount = $money->getAccount();
        $money ->setAccount($moneyAccount + 100);
        $this->moneyRepository->save($money);

        return $this->render('catalog/index.html.twig', [
            'title_page' => 'Vintud - Catalog',
            'articles' => $articles,
            'log' => (bool)$user,
            'filter_form' => $filterForm->createView(),
            'moneyAccount' => $moneyAccount
        ]);
    }
}

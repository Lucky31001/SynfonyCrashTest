<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\FavoriteRepository;
use App\Repository\MoneyRepository;
use App\Repository\OnSaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersFavoriteController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Security $security,
        private CategoryRepository $categoryRepository,
        private MoneyRepository $moneyRepository,
        private OnSaleRepository $onSaleRepository,
        private readonly FavoriteRepository $favoriteRepository,
    ) {
    }
    #[Route('/catalog/favorites/', name: 'app_users_favorite')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyAccount = $money->getAccount();
        }
        $favorites = $this->favoriteRepository->findBy(['user_id' => $user]);
        foreach ($favorites as $favorite) {
            $articleIds[] = $favorite->getAritcleId();
        }        $articles = $this->articleRepository->findBy(['id' => $articleIds]);

        $canDelete = [];
        foreach ($articles as $article) {
            $user = $this->security->getUser();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $article, 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }

        return $this->render('users_favorite/index.html.twig', [
            'title_page' => 'Vintud - Favorite',
            'articles' => $articles,
            'canDelete' => $canDelete,
            'log' => (bool)$user,
            'moneyAccount' => $moneyAccount,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Buy;
use App\Repository\BuyRepository;
use App\Entity\Sell;
use App\Repository\SellRepository;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\OnSaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\MoneyRepository;

class BuyController extends AbstractController
{
    public function __construct(
        private BuyRepository $buyRepository,
        private SellRepository $sellRepository,
        private UserRepository $categoryRepository,
        private ArticleRepository $articleRepository,
        private OnSaleRepository $onSaleRepository,
        private Security $security,
        private MoneyRepository $moneyRepository,
    ) {}

    #[Route('{id}/article/buy/new', name: 'buy_article')]
    public function index(int $id): Response    
    {
        $user = $this->security->getUser();
        $article = $this->articleRepository->find($id);

        $message = 'il faut vous connecter pour acheté';

        if ($user) {
            $onSale = $this->onSaleRepository->findOneBy(['article' => $article]);
            $owner = $onSale->getUser();

            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyOwner = $this->moneyRepository->findOneBy(['user' => $owner]);

            $prix = $article->getPrice();
            $moneyAccount = $money->getAccount();
            $moneyOwnerAccount = $moneyOwner->getAccount();
            $showArticle = $article->isShow();

            $ownerId = $owner->getUserIdentifier();
            $userId = $user->getUserIdentifier();

            if ($userId === $ownerId) {
                $message = 'tu ne peux pas acheter ton propre produit';
            }  else {
                $message = 'Article déjà vendu';
                if ($showArticle) {
                    $message = 'pas assez de soldes';
                    if ($moneyAccount > $prix) {
                        $buys = new Buy();
                        $buys->setUser($user);
                        $buys->setArticle($article);

                        $money->setAccount($moneyAccount - $prix);
                        $moneyOwner->setAccount($moneyOwnerAccount + $prix);
                        $article->setShow(0);

                        $this->buyRepository->save($buys);
                        $this->moneyRepository->save($money);
                        $this->moneyRepository->save($moneyOwner);
                        $this->articleRepository->save($article);

                        $message = 'produit acheté avec succès';
                    }   
                }
            }
        }

        return $this->render('article/show.html.twig', [
            'controller_name' => 'ItemController',
            'article' => $article,
            'message' => $message,
        ]);
    }
}

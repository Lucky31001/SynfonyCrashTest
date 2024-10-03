<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\MoneyRepository;
use App\Repository\NotificationRepository;
use App\Repository\OnSaleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MoneyRepository;

class OnSaleController extends AbstractController
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private OnSaleRepository $onSaleRepository,
        private ArticleRepository $articleRepository,
        private MoneyRepository $moneyRepository,
        private NotificationRepository $notificationRepository
    ) {
    }
    /*
     * @IsGranted("ROLE_USER")
     */
    #[Route('/onSale', name: 'on_sale')]
    public function index(): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $this->security->getUser()->getUserIdentifier()]);
        $onSale = $this->onSaleRepository->findByUserWithArticles($user);
        $tab = [];
        $canDelete = [];
        $user = $this->security->getUser();
        foreach ($onSale as $sale) {
            $tab[] = $sale->getArticle();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $sale->getArticle(), 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }
        $money = $this->moneyRepository->findOneBy(['user' => $this->security->getUser()]);
        $moneyAccount = $money->getAccount();

        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);

        $money = $this->moneyRepository->findOneBy(['user' => $user]);
        $moneyAccount = $money->getAccount();

        return $this->render('on_sale/index.html.twig', [
            'title_page' => 'Vintud - On Sale',
            'onSale' => $tab,
            'log' => (bool)$user,
            'moneyAccount' => $moneyAccount,
            'canDelete' => $canDelete,
            'NewNotification' => $NewNotification
        ]);
    }
}

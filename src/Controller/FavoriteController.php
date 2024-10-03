<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Notification;
use App\Entity\OnSale;
use App\Repository\FavoriteRepository;
use App\Repository\ArticleRepository;
use App\Repository\OnSaleRepository;
use App\Repository\UserRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavoriteController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly Security $security,
        private readonly OnSaleRepository $onSaleRepository,
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    #[Route('/favorite/add/{article_id}', name: 'app_favorite_add')]
    public function index(
        int $article_id,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response {
        $referer = $request->headers->get('referer');
        $favorite = new Favorite();
        $user = $this->security->getUser();
        $favorite->setUserId($user);
        $favorite->setAritcleId($this->articleRepository->findOneBy(['id' => $article_id]));
        if (!$this->security->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $check_existing = $this->favoriteRepository->findOneBy(['article_id' => $article_id, 'user_id' => $user->getId()]);
        if (!$check_existing) {
            $this->favoriteRepository->save($favorite);
            $notification = new Notification();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $favorite->getAritcleId()]);
            $notification->setUser($onsale->getUser());
            $notification->setMessage('Someone added your article ' . $onsale->getArticle()->getTitle() . ' to favorite');
            $this->notificationRepository->save($notification);
        } else {
            $this->favoriteRepository->delete($check_existing);
            $notification = new Notification();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $favorite->getAritcleId()]);
            $notification->setUser($onsale->getUser());
            $notification->setMessage('Someone removed your article ' . $onsale->getArticle()->getTitle() . ' from favorite');
            $this->notificationRepository->save($notification);
        }
        return $this->redirect($referer);
    }
}

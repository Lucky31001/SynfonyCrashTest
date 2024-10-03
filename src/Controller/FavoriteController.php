<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavoriteController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository      $articleRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FavoriteRepository     $favoriteRepository, private readonly Security $security,
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
        } else {
            $this->favoriteRepository->delete($check_existing);
        }
        return $this->redirect($referer);
    }
}

<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\OnSaleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OnSaleController extends AbstractController
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private OnSaleRepository $onSaleRepository,
        private ArticleRepository $articleRepository
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
        $user = $this->security->getUser();
        foreach ($onSale as $sale) {
            $tab[] = $sale->getArticle();
        }

        return $this->render('on_sale/index.html.twig', [
            'title_page' => 'Vintud - On Sale',
            'onSale' => $tab,
            'log' => (bool)$user
        ]);
    }
}

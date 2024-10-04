<?php

namespace App\Controller;

use App\Entity\Buy;
use App\Entity\Message;
use App\Entity\Notification;
use App\Form\MessageType;
use App\Repository\BuyRepository;
use App\Entity\Sell;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\NotificationRepository;
use App\Repository\SellRepository;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\OnSaleRepository;
use App\Service\MessageService;
use Mpdf\Mpdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        private NotificationRepository $notificationRepository,
        private UserRepository $userRepository,
        private ConversationRepository $conversationRepository,
        private MessageService $MessageService
    ) {
    }

    #[Route('{id}/article/buy/new', name: 'buy_article')]
    public function buy(int $id, Request $request): Response
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
            $tva = $article->getTva();
            $ttc = $prix + ($prix * ($tva / 100));


            $moneyAccount = $money->getAccount();
            $moneyOwnerAccount = $moneyOwner->getAccount();
            $showArticle = $article->isShow();

            $ownerId = $owner->getUserIdentifier();
            $userId = $user->getUserIdentifier();

            if ($userId === $ownerId) {
                $message = 'tu ne peux pas acheter ton propre produit';
            } else {
                $message = 'Article déjà vendu';
                if ($showArticle) {
                    $message = 'pas assez de soldes';
                    if ($moneyAccount > $prix) {
                        $buys = new Buy();
                        $buys->setUser($user);
                        $buys->setArticle($article);

                        $sell = new Sell();
                        $sell->setUser($user);
                        $sell->setArticle($article);
                        $sell->setOwner($owner);

                        $money->setAccount($moneyAccount - $ttc);
                        $moneyOwner->setAccount($moneyOwnerAccount + $ttc);

                        $article->setShow(0);

                        $this->buyRepository->save($buys);
                        $this->sellRepository->save($sell);

                        $this->moneyRepository->save($money);
                        $this->moneyRepository->save($moneyOwner);

                        $this->articleRepository->save($article);

                        $message = 'produit acheté avec succès';

                        $notification = new Notification();
                        $notification->setUser($owner);
                        $notification->setMessage('Votre article ' . $article->getTitle() . ' a été acheté');
                        $this->notificationRepository->save($notification);
                    }
                }
            }

            //Ajout du formulaire pour empêcher un potentiel bug
            $sender = $this->userRepository->find($user);
            $receiver = $this->userRepository->find($this->onSaleRepository->find($this->articleRepository->findOneById($id)));
            $conversation = $this->conversationRepository->findOneByUsers($sender, $receiver);
            $messages = new Message();
            $form = $this->createForm(MessageType::class, $messages);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $content = $form->get('content')->getData();
                $form = $this->MessageService->sendMessage($sender, $receiver, $conversation, $messages, $content);
                return $this->redirectToRoute('conversation_show', ['conversationId' => $conversation->getId()]);
            }
        } else {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('article/show.html.twig', [
            'controller_name' => 'ItemController',
            'article' => $article,
            'message' => $message,
            'new_conv' => $form,

        ]);
    }

    #[Route('/my-purchases', name: 'buy_article_show')]
    public function buyPage(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        $money = $this->moneyRepository->findOneBy(['user' => $user]);
        $moneyAccount = $money->getAccount();

        $buys = $this->buyRepository->findBy(['user' => $user]);

        $articles = [];
        if ($buys) {
            foreach ($buys as $buy) {
                $article = $buy->getArticle();
                if ($article) {
                    $prix = $article->getPrice();
                    $tva = $article->getTva();

                    $ttc = $prix + ($prix * ($tva / 100));

                    $article->ttc = $ttc;
                    $articles[] = $article;
                }
            }
        }

        $canDelete = [];
        foreach ($articles as $article) {
            $user = $this->security->getUser();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $article, 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }

        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);

        return $this->render('my-purchases/show.html.twig', [
            'log' => (bool)$user,
            'moneyAccount' => $moneyAccount,
            'articles' => $articles,
            'canDelete' => $canDelete,
            'NewNotification' => $NewNotification,
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/my-sell', name: 'sell_article_show')]
    public function sellPage(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        $money = $this->moneyRepository->findOneBy(['user' => $user]);
        $moneyAccount = $money->getAccount();

        $buys = $this->sellRepository->findBy(['owner' => $user]);

        $articles = [];
        if ($buys) {
            foreach ($buys as $buy) {
                $article = $buy->getArticle();
                if ($article) {
                    $prix = $article->getPrice();
                    $tva = $article->getTva();

                    $ttc = $prix + ($prix * ($tva / 100));

                    $article->ttc = $ttc;
                    $articles[] = $article;
                }
            }
        }

        $canDelete = [];
        foreach ($articles as $article) {
            $user = $this->security->getUser();
            $onsale = $this->onSaleRepository->findOneBy(['article' => $article, 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }

        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);


        return $this->render('my-sell/show.html.twig', [
            'log' => (bool)$user,
            'moneyAccount' => $moneyAccount,
            'articles' => $articles,
            'canDelete' => $canDelete,
            'NewNotification' => $NewNotification
        ]);
    }

    #[Route('/my-sell/pdf', name: 'sell_article_pdf')]
    public function sellPagePdf(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $money = $this->moneyRepository->findOneBy(['user' => $user]);
        $moneyAccount = $money->getAccount();

        $buys = $this->sellRepository->findBy(['owner' => $user]);

        $articles = [];
        if ($buys) {
            foreach ($buys as $buy) {
                $article = $buy->getArticle();
                if ($article) {
                    $prix = $article->getPrice();
                    $tva = $article->getTva();

                    $ttc = $prix + ($prix * ($tva / 100));

                    $article->ttc = $ttc;
                    $articles[] = $article;
                }
            }
        }

        $canDelete = [];
        foreach ($articles as $article) {
            $onsale = $this->onSaleRepository->findOneBy(['article' => $article, 'user' => $user]);
            $canDelete[] = (bool)$onsale;
        }

        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);

        // Render the HTML content
        $html = $this->renderView('my-sell/show.html.twig', [
            'log' => (bool)$user,
            'moneyAccount' => $moneyAccount,
            'articles' => $articles,
            'canDelete' => $canDelete,
            'NewNotification' => $NewNotification
        ]);

        // Instantiate mPDF
        $mpdf = new Mpdf();

        // Load HTML to mPDF
        $mpdf->WriteHTML($html);

        // Output the generated PDF to Browser (force download)
        $mpdf->Output("my-sales.pdf", "D");

        return new Response();
    }
}

<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\OnSale;
use App\Form\ArticleForm;
use App\Form\MessageType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\MoneyRepository;
use App\Repository\NotificationRepository;
use App\Repository\OnSaleRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository,
        private OnSaleRepository $onSaleRepository,
        private Security $security,
        private UserRepository $userRepository,
        private ConversationRepository $conversationRepository,
        private MoneyRepository $moneyRepository,
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
        private MessageRepository $messageRepository,
        private MessageService $MessageService
    ) {
    }
    #[Route('/create/article/', name: 'create_article')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = new Article();
            $article->setTitle($form->get('title')->getData());
            $article->setContent($form->get('content')->getData());
            if ($form->get('image')->getData() != null) {
                $article->setImage($form->get('image')->getData());
            }
            $article->setPrice($form->get('price')->getData());
            $article->setTva(20);
            $article->setShow(1);
            $article->setCategory($form->get('category')->getData());
            $this->articleRepository->save($article);

            $user = $this->security->getUser();
            $onSale = new OnSale();
            $onSale->setArticle($article);
            $onSale->setUser($user);
            $this->onSaleRepository->save($onSale);

            return $this->redirectToRoute('article_success');
        }

        return $this->render('article/createArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/success/article', name: 'article_success')]
    public function success(): Response
    {
        return $this->render('article/success.html.twig');
    }

    #[Route('/modif/article/{id}', name: 'modif_article')]
    public function modif(int $id, Request $request): Response
    {
        $article = $this->articleRepository->find($id);

        $form = $this->createForm(ArticleForm::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setTitle($form->get('title')->getData());
            $article->setContent($form->get('content')->getData());
            if ($form->get('image')->getData() != null) {
                $article->setImage($form->get('image')->getData());
            }
            $article->setPrice($form->get('price')->getData());
            $category = $this->categoryRepository->find($form->get('category')->getData()->getId());
            $article->setCategory($category);
            $this->articleRepository->save($article);

            return $this->redirectToRoute('show_article', ['id' => $id]);
        }

        return $this->render('article/modifArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/article/{id}', name: 'delete_article')]
    public function delete(int $id)
    {
        $article = $this->onSaleRepository->findBy(['article' => $id])[0];
        $this->onSaleRepository->delete($article);
        return $this->redirectToRoute('catalog');
    }

    #[Route('/{id}/article', name: 'show_article')]
    public function show(int $id, Request $request): Response
    {

        $user = $this->security->getUser();
        $sender = $this->userRepository->find($user);
        $receiver = $this->userRepository->find($this->onSaleRepository->findOneBy(['article' => $this->articleRepository->find($id)])->getUser());
        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);

        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyAccount = $money->getAccount();
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conversation = $this->conversationRepository->findOneByUsers($sender, $receiver);
            if ($sender === $receiver) {
                return $this->redirectToRoute('app_login');
            }
            $content = $form->get('content')->getData();
            $form = $this->MessageService->sendMessage($sender, $receiver, $conversation, $message, $content);
            $messages = $this->messageRepository->findBy(['conversation' => $conversation]);
            return $this->redirectToRoute('conversation_show', ['conversationId' => $conversation->getId()]);
        }

        return $this->render('article/show.html.twig', [
            'controller_name' => 'ItemController',
            'article' => $this->articleRepository->find($id),
            'message' => null,
            'new_conv' => $form,
            'NewNotification' => $NewNotification,
            'moneyAccount' => $moneyAccount,
        ]);
    }
}

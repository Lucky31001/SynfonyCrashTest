<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Form\MessageType;
use App\Repository\MoneyRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ConversationRepository $conversationRepository,
        private readonly UserRepository $userRepository,
        private readonly Security $security,
        private readonly MoneyRepository $moneyRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    #[Route('/conversation/{conversationId}', name: 'conversation_show')]
    public function showConversation(Request $request, int $conversationId): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $sender = $this->userRepository->find($user);
        $receiver = $this->conversationRepository->findOneById($conversationId)->getUserTwo();
        if ($sender === $receiver) {
            $receiver = $this->conversationRepository->findOneById($conversationId)->getUserOne();
        }
        $conversation = $this->conversationRepository->findOneByUsers($sender, $receiver);

        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyAccount = $money->getAccount();
        }

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setUserOne($sender);
            $conversation->setUserTwo($receiver);
            $this->entityManager->persist($conversation);
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->get('content')->getData();
            $message->setConversation($conversation);
            $message->setSender($sender);
            $message->setReceiver($receiver);
            $message->setContent($content);
            $message->setCreatedAt(new \DateTime());
            $this->entityManager->persist($message);
            $this->entityManager->flush();
            return $this->redirectToRoute('conversation_show', ['conversationId' => $conversation->getId()]);
        }
        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);
        $messages = $this->messageRepository->findBy(['conversation' => $conversation]);

        return $this->render('message/index.html.twig', [
            'title_page' => 'Vintud - Messagerie',
            'log' => (bool)$user,
           'conversation' => $conversation,
            'NewNotification' => $NewNotification,
            'moneyAccount' => $moneyAccount,
            'messages' => $messages,
            'form_message' => $form,
            'email' => $user->getEmail(),

        ]);
    }

    #[Route('/show_conversation', name: 'all_conversation')]
    public function showAllConversation(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $money = $this->moneyRepository->findOneBy(['user' => $user]);
        $moneyAccount = $money->getAccount();
        $conversations = $this->conversationRepository->findByUser($user);
        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);
        return $this->render('conversation/index.html.twig', [
            'title_page' => 'Vintud - Conversations',
            'log' => (bool)$user,
            'conversations' => $conversations,
            'NewNotification' => $NewNotification,
            'moneyAccount' => $moneyAccount,
            'email' => $user->getEmail(),
        ]);
    }
}

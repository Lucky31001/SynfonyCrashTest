<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MoneyRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class MessageService
{

    public function __construct(
        private LoggerInterface $logger,
        private readonly MessageRepository $messageRepository,
        private readonly ConversationRepository $conversationRepository,
        private readonly UserRepository $userRepository,
        private readonly Security $security,
        private readonly MoneyRepository $moneyRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationRepository $notificationRepository
    ) {
    }
    public function sendMessage(User $receiver, User $sender, Conversation $conversation, Message $message, string $content)
    {
        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setUserOne($sender);
            $conversation->setUserTwo($receiver);
            $this->entityManager->persist($conversation);
        }
        $message->setConversation($conversation);
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTime());
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function checkReceiver(User $receiver, User $sender, int $conversationId)
    {
        if ($sender === $receiver) {
            $receiver = $this->conversationRepository->findOneById($conversationId)->getUserOne();
        }
        return $receiver;
    }
}

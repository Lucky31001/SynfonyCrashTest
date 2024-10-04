<?php

namespace App\Controller;

use App\Repository\MoneyRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security,
        private MoneyRepository $moneyRepository
    ) {
    }
    #[Route('/notification', name: 'notification')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $NewNotification = $this->notificationRepository->count(['user' => $user, 'isRead' => false]);
        $moneyAccount = 0;
        if ($user) {
            $money = $this->moneyRepository->findOneBy(['user' => $user]);
            $moneyAccount = $money->getAccount();
        }

        $notifications = $this->notificationRepository->findBy(['user' => $user]);
        return $this->render('notification/index.html.twig', [
            'title_page' => 'Vintud - Notification',
            'notifications' => $notifications,
            'NewNotification' =>$NewNotification,
            'log' => (bool)$user,
            'moneyAccount' => $moneyAccount,
        ]);
    }

    #[Route('/notification/{id}', name: 'notification_read')]
    public function update(int $id): Response
    {
        $notification = $this->notificationRepository->find($id);
        $notification->setIsRead(true);
        $this->notificationRepository->save($notification);
        return $this->redirectToRoute('notification');
    }
}

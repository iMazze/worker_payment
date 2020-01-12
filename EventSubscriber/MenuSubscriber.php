<?php
/*
 * This file is part of the worker_payment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\worker_payment\EventSubscriber;

use App\Event\ConfigureMainMenuEvent;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use KimaiPlugin\worker_payment\Repository\WorkerPaymentRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $security;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var WorkerPaymentRepository
     */
    private $workerPaymentRepository;

    /**
     * MenuSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $security
     * @param WorkerPaymentRepository $workerPaymentRepository
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $security, WorkerPaymentRepository $workerPaymentRepository)
    {
        $this->security = $security;
        $this->tokenStorage = $tokenStorage;
        $this->workerPaymentRepository = $workerPaymentRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMainMenuEvent::class => ['onMenuConfigure', 100]
        ];
    }

    /**
     * @param ConfigureMainMenuEvent $event
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function onMenuConfigure(ConfigureMainMenuEvent $event)
    {
        $auth = $this->security;

        if (!$auth->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return;
        }

        $menu = $event->getSystemMenu();

        if ($auth->isGranted('ROLE_SUPER_ADMIN') || $auth->isGranted('worker_payment')) {
            $workerPaymentCounter = $this->workerPaymentRepository->getAllworkerPaymentCounter();
            $badgeColor = ($workerPaymentCounter > 0 ? 'orange' : 'green');
            $menu->addChild(
                new MenuItemModel('worker_payment', 'workerpayment.title', 'worker_payment', [], 'fas fa-book', $workerPaymentCounter, $badgeColor)
            );
        }
    }
}

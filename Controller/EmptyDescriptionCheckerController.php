<?php
/*
 * This file is part of the EmptyDescriptionCheckerBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\EmptyDescriptionCheckerBundle\Controller;

use App\Controller\AbstractController;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Repository\Query\TimesheetQuery;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use KimaiPlugin\EmptyDescriptionCheckerBundle\Repository\EmptyDescriptionCheckerRepository;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/empty-description-checker")
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('empty_description_checker')")
 */
class EmptyDescriptionCheckerController extends AbstractController
{
    /**
     * @var EmptyDescriptionCheckerRepository
     */
    private $repository;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * EmptyDescriptionCheckerController constructor.
     * @param EmptyDescriptionCheckerRepository $repository
     * @param Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(EmptyDescriptionCheckerRepository $repository, Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @Route(path="", defaults={"page": 1}, name="empty_description_checker", methods={"GET"})
     * @Route(path="/page/{page}", requirements={"page": "[1-9]\d*"}, name="empty_description_checker_paginated", methods={"GET"})
     *
     * @param int $page
     * @return Response
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function indexAction($page)
    {
        $timeSheetQuery = new TimesheetQuery();
        $timeSheetQuery->setPage($page);

        /** @var Pagerfanta $entries */
        $entries = $this->repository->getAllEmptyDescriptionsPaginated($timeSheetQuery);

        return $this->render('@EmptyDescriptionChecker/index.html.twig', [
            'entries' => $entries,
            'page' => $page
        ]);
    }

    /**
     * @Route(path="/send-emails", name="empty_description_checker_send_emails", methods={"GET"})
     *
     * @param bool $returnView
     * @return int|Response
     * @throws Exception
     */
    public function sendEmailsAction(bool $returnView = true)
    {
        $entriesToSendViaMail = $this->getEntriesToSendViaMail();

        if (!empty($entriesToSendViaMail)) {
            $this->sendEntriesWithoutDescriptionToUser($entriesToSendViaMail);
        }

        if ($returnView) {
            return $this->render('@EmptyDescriptionChecker/sendEmails.html.twig', []);
        };

        return 0;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getEntriesToSendViaMail(): array
    {
        /** @var Timesheet $entriesToSend */
        $entriesToSend = $this->repository->getAllEmptyDescriptions();

        if (!empty($entriesToSend)) {
            $sortedEntriesToSend = [];

            /** @var Timesheet $entryToSend */
            foreach ($entriesToSend AS $entryToSend) {
                $userId = $entryToSend->getUser()->getId();
                $sortedEntryToSend = [
                    'begin' => (new DateTime())->setTimestamp($entryToSend->getBegin()->getTimestamp()),
                    'end' => (new DateTime())->setTimestamp($entryToSend->getEnd()->getTimestamp()),
                    'project' => $entryToSend->getProject()->getName(),
                    'activity' => $entryToSend->getActivity()->getName()
                ];

                if (!array_key_exists($userId, $sortedEntriesToSend)) {
                    $sortedEntriesToSend[$entryToSend->getUser()->getId()] = [];
                }
                array_push($sortedEntriesToSend[$userId], $sortedEntryToSend);
            }

            return $sortedEntriesToSend;
        }

        return [];
    }

    /**
     * @param array $entries
     */
    private function sendEntriesWithoutDescriptionToUser(array $entries)
    {
        foreach ($entries AS $userId => $allEntriesOfUser) {
            /** @var User $user */
            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

            $body = $this->renderView(
                '@EmptyDescriptionChecker/emailWithEmptyDescriptions.html.twig', [
                    'allEntriesOfUser' => $allEntriesOfUser,
                    'locale' => $this->translator->setLocale($user->getLocale())
                ]
            );

            $this->sendEmail($user->getEmail(), $this->translator->trans('emptydescriptionchecker.email.subject'), $body);
        }
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     */
    private function sendEmail(string $to, string $subject, string $body)
    {
        $message = (new Swift_Message($subject))
            ->setFrom(getenv('MAILER_FROM'), 'Kimai 2')
            ->setTo($to)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}

<?php
/*
 * This file is part of the worker_payment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\worker_payment\Command;

use Exception;
use KimaiPlugin\worker_payment\Controller\WorkerPaymentController;
use KimaiPlugin\worker_payment\Repository\WorkerPaymentRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to send notification emails to users if they have missing descriptions in their tracked times
 */
class WorkerPaymentCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'emptydescriptionchecker:sendmails';
    /**
     * @var WorkerPaymentRepository
     */
    private $emptyDescriptionCheckerRepository;
    /**
     * @var WorkerPaymentController
     */
    private $emptyDescriptionCheckerController;

    /**
     * WorkerPaymentCommand constructor.
     * @param WorkerPaymentRepository $emptyDescriptionCheckerRepository
     * @param WorkerPaymentController $emptyDescriptionCheckerController
     */
    public function __construct(WorkerPaymentRepository $emptyDescriptionCheckerRepository, WorkerPaymentController $emptyDescriptionCheckerController)
    {
        $this->emptyDescriptionCheckerRepository = $emptyDescriptionCheckerRepository;
        $this->emptyDescriptionCheckerController = $emptyDescriptionCheckerController;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Generate notification email about missing descriptions')
            ->setHelp('Command used to send notification emails to users if they have missing descriptions in their tracked times.');
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = $this->emptyDescriptionCheckerRepository->getAllEmptyDescriptionCounter();

        if ($counter > 0) {
            $this->emptyDescriptionCheckerController->sendEmailsAction(false);
        }

        return 0;
    }
}

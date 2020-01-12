<?php
/*
 * This file is part of the WorkerPayment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\WorkerPayment\Command;

use Exception;
use KimaiPlugin\WorkerPayment\Controller\WorkerPaymentController;
use KimaiPlugin\WorkerPayment\Repository\WorkerPaymentRepository;
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
    protected static $defaultName = 'workerpayment:sendmails';
    /**
     * @var WorkerPaymentRepository
     */
    private $workerPaymentRepository;
    /**
     * @var WorkerPaymentController
     */
    private $workerPaymentController;

    /**
     * WorkerPaymentCommand constructor.
     * @param WorkerPaymentRepository $workerPaymentRepository
     * @param WorkerPaymentController $workerPaymentController
     */
    public function __construct(WorkerPaymentRepository $workerPaymentRepository, WorkerPaymentController $workerPaymentController)
    {
        $this->workerPaymentRepository = $workerPaymentRepository;
        $this->workerPaymentController = $workerPaymentController;

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
        $counter = $this->workerPaymentRepository->getAllworkerPaymentCounter();

        if ($counter > 0) {
            $this->workerPaymentController->sendEmailsAction(false);
        }

        return 0;
    }
}

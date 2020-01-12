<?php
/*
 * This file is part of the worker_payment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace KimaiPlugin\worker_payment\API;

use App\API\BaseApiController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use KimaiPlugin\worker_payment\Repository\WorkerPaymentRepository;

class WorkerPaymentApiController extends BaseApiController
{
    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;
    /**
     * @var WorkerPaymentRepository
     */
    private $workerPaymentRepository;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param WorkerPaymentRepository $workerPaymentRepository
     */
    public function __construct(ViewHandlerInterface $viewHandler, WorkerPaymentRepository $workerPaymentRepository)
    {
        $this->viewHandler = $viewHandler;
        $this->workerPaymentRepository = $workerPaymentRepository;
    }

    /**
     * @Rest\Get(path="/worker_payment/counter")
     */
    public function counterAction()
    {
        $view = new View(['counter' => $this->workerPaymentRepository->getAllworkerPaymentCounter()], 200);

        return $this->viewHandler->handle($view);
    }
}

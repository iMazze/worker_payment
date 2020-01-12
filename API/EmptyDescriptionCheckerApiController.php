<?php
/*
 * This file is part of the EmptyDescriptionCheckerBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace KimaiPlugin\EmptyDescriptionCheckerBundle\API;

use App\API\BaseApiController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use KimaiPlugin\EmptyDescriptionCheckerBundle\Repository\EmptyDescriptionCheckerRepository;

class EmptyDescriptionCheckerApiController extends BaseApiController
{
    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;
    /**
     * @var EmptyDescriptionCheckerRepository
     */
    private $emptyDescriptionCheckerRepository;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param EmptyDescriptionCheckerRepository $emptyDescriptionCheckerRepository
     */
    public function __construct(ViewHandlerInterface $viewHandler, EmptyDescriptionCheckerRepository $emptyDescriptionCheckerRepository)
    {
        $this->viewHandler = $viewHandler;
        $this->emptyDescriptionCheckerRepository = $emptyDescriptionCheckerRepository;
    }

    /**
     * @Rest\Get(path="/empty-description-checker/counter")
     */
    public function counterAction()
    {
        $view = new View(['counter' => $this->emptyDescriptionCheckerRepository->getAllEmptyDescriptionCounter()], 200);

        return $this->viewHandler->handle($view);
    }
}

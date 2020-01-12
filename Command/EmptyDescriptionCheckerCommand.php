<?php
/*
 * This file is part of the EmptyDescriptionCheckerBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\EmptyDescriptionCheckerBundle\Command;

use Exception;
use KimaiPlugin\EmptyDescriptionCheckerBundle\Controller\EmptyDescriptionCheckerController;
use KimaiPlugin\EmptyDescriptionCheckerBundle\Repository\EmptyDescriptionCheckerRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to send notification emails to users if they have missing descriptions in their tracked times
 */
class EmptyDescriptionCheckerCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'emptydescriptionchecker:sendmails';
    /**
     * @var EmptyDescriptionCheckerRepository
     */
    private $emptyDescriptionCheckerRepository;
    /**
     * @var EmptyDescriptionCheckerController
     */
    private $emptyDescriptionCheckerController;

    /**
     * EmptyDescriptionCheckerCommand constructor.
     * @param EmptyDescriptionCheckerRepository $emptyDescriptionCheckerRepository
     * @param EmptyDescriptionCheckerController $emptyDescriptionCheckerController
     */
    public function __construct(EmptyDescriptionCheckerRepository $emptyDescriptionCheckerRepository, EmptyDescriptionCheckerController $emptyDescriptionCheckerController)
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

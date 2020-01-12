<?php
/*
 * This file is part of the worker_payment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\worker_payment\Repository;

use App\Entity\Timesheet;
use App\Repository\Loader\TimesheetLoader;
use App\Repository\Paginator\LoaderPaginator;
use App\Repository\Paginator\PaginatorInterface;
use App\Repository\Query\TimesheetQuery;
use App\Repository\TimesheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;

class WorkerPaymentRepository
{
    /**
     * @var TimesheetRepository
     */
    private $repository;
    /**
     * @var UsersToExcludeRepository
     */
    private $usersToExcludeRepository;
    /**
     * @var ProjectsToExcludeRepository
     */
    private $projectsToExcludeRepository;
    /**
     * @var ProjectsToExcludeRepository
     */
    private $customersToExcludeRepository;

    /**
     * WorkerPaymentRepository constructor.
     * @param EntityManagerInterface $entityManager
     * @param UsersToExcludeRepository $usersToExcludeRepository
     * @param ProjectsToExcludeRepository $projectsToExcludeRepository
     * @param CustomersToExcludeRepository $customersToExcludeRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UsersToExcludeRepository $usersToExcludeRepository, ProjectsToExcludeRepository $projectsToExcludeRepository, CustomersToExcludeRepository $customersToExcludeRepository)
    {
        $this->repository = $entityManager->getRepository(Timesheet::class);
        $this->usersToExcludeRepository = $usersToExcludeRepository;
        $this->projectsToExcludeRepository = $projectsToExcludeRepository;
        $this->customersToExcludeRepository = $customersToExcludeRepository;
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getAllEmptyDescriptionCounter()
    {
        $qb = $this->getAllEmptyDescriptionsQueryBuilder();

        $qb
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->select($qb->expr()->countDistinct('t.id'));

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return mixed
     */
    public function getAllEmptyDescriptions()
    {
        $qb = $this->getAllEmptyDescriptionsQueryBuilder()->select();
        return $qb->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function getAllEmptyDescriptionsQueryBuilder()
    {
        $projectsOfCustomersToExclude = $this->customersToExcludeRepository->getProjectsOfCustomersToExclude();
        $usersToExclude = $this->usersToExcludeRepository->getUsersToExclude();
        $projectsToExclude = $this->projectsToExcludeRepository->getProjectsToExclude();

        $qb = $this->repository->createQueryBuilder('t');

        $qb->select('t')
            ->join('t.user', 'user')
            ->where($qb->expr()->isNull('t.description'))
            ->orWhere('t.description = :search')->setParameter('search', ' ')
            ->andWhere('t.end IS NOT NULL')
            ->andWhere('user.enabled = 1')
            ->andWhere('t.user NOT IN (' . $usersToExclude . ')')
            ->andWhere('t.project NOT IN (' . $projectsToExclude . ')')
            ->andWhere('t.project NOT IN (' . $projectsOfCustomersToExclude . ')')
            ->orderBy('t.begin', 'ASC');

        return $qb;
    }

    /**
     * @param TimesheetQuery $query
     * @return Pagerfanta
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getAllEmptyDescriptionsPaginated(TimesheetQuery $query)
    {
        return $this->getPagerfantaForQuery($query);
    }

    /**
     * @param TimesheetQuery $query
     * @return Pagerfanta
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPagerfantaForQuery(TimesheetQuery $query): Pagerfanta
    {
        $paginator = new Pagerfanta($this->getAllEmptyDescriptionsPaginator());
        $paginator->setMaxPerPage($query->getPageSize());
        $paginator->setCurrentPage($query->getPage());

        return $paginator;
    }

    /**
     * @return PaginatorInterface
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function getAllEmptyDescriptionsPaginator(): PaginatorInterface
    {
        $counter = $this->getAllEmptyDescriptionCounter();
        $qb = $this->getAllEmptyDescriptionsQueryBuilder();

        return new LoaderPaginator(new TimesheetLoader($qb->getEntityManager()), $qb, $counter);
    }
}

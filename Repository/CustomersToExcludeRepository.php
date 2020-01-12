<?php
/*
 * This file is part of the worker_payment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\worker_payment\Repository;

use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;

class CustomersToExcludeRepository
{
    /**
     * @var string
     */
    private $customersToExcludeConf;
    /**
     * @var string
     */
    private $dataDirectory;
    /**
     * @var string
     */
    private $folderName = 'worker_payment';
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @param string $dataDirectory
     * @param ProjectRepository $projectRepository
     */
    public function __construct(string $dataDirectory, ProjectRepository $projectRepository)
    {
        $this->dataDirectory = $dataDirectory;
        $this->customersToExcludeConf = $dataDirectory . '/' . $this->folderName . '/customers_to_exclude.conf';
        $this->projectRepository = $projectRepository;
    }

    /**
     * @return string
     */
    public function getProjectsOfCustomersToExclude(): string
    {
        if (!file_exists($this->dataDirectory . '/' . $this->folderName)) {
            mkdir($this->dataDirectory . '/' . $this->folderName);
        }

        if (!file_exists($this->customersToExcludeConf)) {
            $confFile = fopen($this->customersToExcludeConf, "w");
            fwrite($confFile, '### DO NOT REMOVE THIS LINE! ### If you want to exclude customers from checking about missing descriptions please put their customerIds separated by , (it is a comma - NOT a semicolon!) in the SECOND line (the line after this line) in this file. You may want to include this file in your backup!');
            fclose($confFile);
        }

        $customersToExcludeFromConfig = file($this->customersToExcludeConf);

        if (isset($customersToExcludeFromConfig[1])) {
            return $this->getAllProjectIdsOfCustomersToExclude($customersToExcludeFromConfig[1]);
        }

        return '0';
    }

    /**
     * @param string $customerIds
     * @return string
     */
    private function getAllProjectIdsOfCustomersToExclude(string $customerIds): string
    {
        $return = '';
        $qb = $this->getAllProjectsOfCustomersQueryBuilder($customerIds)->select();
        $result = $qb->getQuery()->getResult();

        foreach ($result AS $projectId) {
            $return .= $projectId['id'] . ',';
        }

        return rtrim($return, ',');
    }

    /**
     * @param string $customerIds
     * @return QueryBuilder
     */
    private function getAllProjectsOfCustomersQueryBuilder(string $customerIds)
    {
        $qb = $this->projectRepository->createQueryBuilder('p');
        $qb->select('p.id')
            ->where('p.customer IN (' . $customerIds . ')');

        return $qb;
    }
}

<?php
/*
 * This file is part of the worker_payment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\worker_payment\Repository;

class UsersToExcludeRepository
{
    /**
     * @var string
     */
    private $usersToExcludeConf;
    /**
     * @var string
     */
    private $dataDirectory;
    /**
     * @var string
     */
    private $folderName = 'worker_payment';

    /**
     * @param string $dataDirectory
     */
    public function __construct(string $dataDirectory)
    {
        $this->dataDirectory = $dataDirectory;
        $this->usersToExcludeConf = $dataDirectory . '/' . $this->folderName . '/users_to_exclude.conf';
    }

    /**
     * @return string
     */
    public function getUsersToExclude(): string
    {
        if (!file_exists($this->dataDirectory . '/' . $this->folderName)) {
            mkdir($this->dataDirectory . '/' . $this->folderName);
        }

        if (!file_exists($this->usersToExcludeConf)) {
            $confFile = fopen($this->usersToExcludeConf, "w");
            fwrite($confFile, '### DO NOT REMOVE THIS LINE! ### If you want to exclude users from notifying about missing descriptions please put their usersIds separated by , (it is a comma - NOT a semicolon!) in the SECOND line (the line after this line) in this file. You may want to include this file in your backup!');
            fclose($confFile);
        }

        $usersToExcludeFromConfig = file($this->usersToExcludeConf);

        if (isset($usersToExcludeFromConfig[1])) {
            return rtrim($usersToExcludeFromConfig[1], ',');
        }

        return '0';
    }
}

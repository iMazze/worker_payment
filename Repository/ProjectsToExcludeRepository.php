<?php
/*
 * This file is part of the EmptyDescriptionCheckerBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\EmptyDescriptionCheckerBundle\Repository;

class ProjectsToExcludeRepository
{
    /**
     * @var string
     */
    private $projectsToExcludeConf;
    /**
     * @var string
     */
    private $dataDirectory;
    /**
     * @var string
     */
    private $folderName = 'EmptyDescriptionCheckerBundle';

    /**
     * @param string $dataDirectory
     */
    public function __construct(string $dataDirectory)
    {
        $this->dataDirectory = $dataDirectory;
        $this->projectsToExcludeConf = $dataDirectory . '/' . $this->folderName . '/projects_to_exclude.conf';
    }

    /**
     * @return string
     */
    public function getProjectsToExclude(): string
    {
        if (!file_exists($this->dataDirectory . '/' . $this->folderName)) {
            mkdir($this->dataDirectory . '/' . $this->folderName);
        }

        if (!file_exists($this->projectsToExcludeConf)) {
            $confFile = fopen($this->projectsToExcludeConf, "w");
            fwrite($confFile, '### DO NOT REMOVE THIS LINE! ### If you want to exclude projects from checking about missing descriptions please put their projectIds separated by , (it is a comma - NOT a semicolon!) in the SECOND line (the line after this line) in this file. You may want to include this file in your backup!');
            fclose($confFile);
        }

        $projectsToExcludeFromConfig = file($this->projectsToExcludeConf);

        if (isset($projectsToExcludeFromConfig[1])) {
            return rtrim($projectsToExcludeFromConfig[1], ',');
        }

        return '0';
    }
}

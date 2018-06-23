<?php

namespace Microbeasts\LocalRunnerCore\Steps;

use Symfony\Component\Process\Process;

class CloneRepositories extends AbstractStep
{

    /**
     * Clone all repositories in a selected project
     *
     * @param string $project
     * @param array  $projectConfig
     */
    protected function _run(string $project, array $projectConfig)
    {
        $repositories = $projectConfig['repositories'];

        // Work with git
        if (!empty($repositories['git'])) {
            foreach ($repositories['git'] as $repositoryKey => $repository) {
                $repositoryPath = $this->makeRepositoryPath($project, [$repositoryKey => $repository]);
                if (!is_dir($repositoryPath)) {
                    $process = new Process('git clone ' . $repository . ' ' . $repositoryPath);
                    $process->run();
                }
            }
        }

        // Other repositories
        // ...
    }

    /**
     * Make repository path
     *
     * @param string $project - project name
     * @param array $repository - name and link to repository
     */
    private function makeRepositoryPath(string $project, $repository)
    {
        return $this->getProjectPath($project) . '/' . $this->makeRepositoryName($repository);
    }

}
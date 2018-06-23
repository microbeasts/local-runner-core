<?php

namespace Microbeasts\LocalRunnerCore\Steps;

use Organelles\System\Hosts;
use Symfony\Component\Process\Process;

class DownDockerCompose extends AbstractStep
{

    /**
     * Stop all hosts in a selected project
     *
     * @param string $project
     * @param array  $projectConfig
     */
    protected function _run(string $project, array $projectConfig)
    {
        $process = new Process('docker-compose -f ' . $this->getDockerComposeYmlPath($project) . ' down');
        if (0 !== $process->run()) {
            throw new \Exception($process->getErrorOutput());
        }

        $this->deleteHosts($project);

        return (string)$process->getOutput();
    }


    /**
     * Delete hosts to host file
     *
     * @param string $project
     */
    private function deleteHosts($project)
    {
        $hostObject = Hosts::factory();
        foreach ($this->config[$project]['repositories'] as $repositories) {
            foreach ($repositories as $repositoryName => $repositoryPath) {
                $hostObject->delete($this->makeRepositoryName([$repositoryName => $repositoryPath]));
            }
        }
    }

}
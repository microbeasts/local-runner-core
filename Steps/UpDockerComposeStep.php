<?php

namespace Microbeasts\LocalRunnerCore\Steps;

use Organelles\System\Hosts;
use Symfony\Component\Process\Process;

class UpDockerComposeStep extends AbstractStep
{

    /**
     * Start all hosts in a selected project
     *
     * @param string $project
     * @param array $projectConfig
     * @return string
     *
     * @throws \Exception
     */
    protected function _run(string $project, array $projectConfig)
    {
        $process = new Process('docker-compose -f "' . $this->getDockerComposeYmlPath($project) .
            '" up -d --force-recreate');

        if (0 !== $process->run()) {
            throw new \Exception($process->getErrorOutput());
        }

        $this->addHosts($project);

        return (string)$process->getOutput();
    }

    /**
     * Add hosts to host file
     *
     * @param string $project
     *
     * @throws \Exception
     */
    private function addHosts(string $project)
    {
        $hostObject = Hosts::factory();
        foreach ($this->config[$project]['repositories'] as $repositoryName => $repositoryConfig) {
            $hostObject->add($this->makeHostNames([$repositoryName => $repositoryConfig]));
        }
    }

}
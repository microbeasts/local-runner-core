<?php

namespace Microbeasts\LocalRunnerCore\Steps;

use Symfony\Component\Yaml\Yaml;

class BuildDockerComposeYml extends AbstractStep
{

    /**
     * Build docker-compose.yml
     *
     * @param string $project
     * @param array  $projectConfig
     */
    protected function _run(string $project, array $projectConfig)
    {
        // Build a part with version
        $config['version'] = $projectConfig['version'];

        // Build a part with services
        $config['services'] = $projectConfig['services'];

        // Build a part with networks
        if (!empty($projectConfig['networks'])) {
            $config['networks'] = $projectConfig['networks'];
        }

        // Build a part with php-fpm
        foreach ($this->getRepositoriesConfigs($project) as $repositoryConfig) {

        }

        $this->saveDockerComposeYml($project, $config);
    }


    /**
     * Inspect repositories and get their configs
     *
     * @param string $project
     * @return array
     */
    private function getRepositoriesConfigs(string $project): array
    {
        $configs = [];

        $listDirectories = scandir($this->getProjectPath($project));
        foreach ($listDirectories as $directory) {
            $configPath = $directory . 'local-runner.yml';
            if (is_dir($directory) && file_exists($configPath)) {
                $configs[] = Yaml::parse(file_get_contents($configPath));
            }
        }

        return $configs;
    }

    /**
     * Save docker-compose.yml
     *
     * @param string $project
     * @param array  $config
     */
    private function saveDockerComposeYml(string $project, array $config)
    {
        $configDump = Yaml::dump($config, 10);
        file_put_contents($this->getDockerComposeYmlPath($project), $configDump);
    }

}
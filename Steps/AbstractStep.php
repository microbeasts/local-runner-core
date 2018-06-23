<?php

namespace Microbeasts\LocalRunnerCore\Steps;

use Illuminate\Support\Facades\Config;

abstract class AbstractStep
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $project;

    /**
     * StepAbstract constructor.
     *
     * @param string $project - name of the project or 'all' if you want to use all projects at once
     */
    public function __construct(string $project)
    {
        $config = Config::get('local-runner.projects');

        if ('all' != $project and empty($config[$project])) {
            throw new \Exception('There isn\'t config with name ' . $project . ' in local-runner.php');
        }

        $this->config  = $config;
        $this->project = $project;
    }

    /**
     * Get project path
     *
     * @param string $project
     * @return string
     */
    protected function getProjectPath(string $project): string
    {
        return base_path() . '/../' . $project;
    }

    /**
     * Get docker-compose.yml path
     *
     * @param string $project
     * @return string
     */
    protected function getDockerComposeYmlPath(string $project): string
    {
        return base_path() . '/docker-compose.' . $project . '.yml';
    }

    /**
     * Run step
     *
     * @return string
     */
    public function run(): string
    {
        $result = '';
        if ('all' == $this->project) {
            foreach ($this->config as $project => $projectConfig) {
                $result .= $this->_run($project, $projectConfig);
            }
        } else {
            $result .=  $this->_run($this->project, $this->config[$this->project]);
        }

        return $result;
    }

    /**
     * Make repository name
     *
     * @param array $repository - name and link to repository
     */
    protected function makeRepositoryName($repository)
    {
        $repositoryKey  = key($repository);
        $repositoryPath = current($repository);

        if (!is_numeric($repositoryKey)) {
            $repositoryName = $repositoryKey;
        } else {
            $repositoryName = substr($repositoryPath,
                strrpos($repositoryPath, '/') + 1,
                strrpos($repositoryPath, '.') - strrpos($repositoryPath, '/') - 1
            );
        }

        return $repositoryName;
    }

    /**
     * Run step
     *
     * @param string $project
     * @param array  $projectConfig
     */
    abstract protected function _run(string $project, array $projectConfig);

}
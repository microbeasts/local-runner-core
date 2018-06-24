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
     * @param array $repository - name => config
     * @return string
     *
     * @throws \Exception
     */
    protected function makeRepositoryName(array $repository): string
    {
        $repositoryKey    = key($repository);
        $repositoryConfig = current($repository);

        if (!is_numeric($repositoryKey)) {
            return (string)$repositoryKey;
        } elseif (!empty($repositoryConfig['path'])) {
            return (string) substr($repositoryConfig['path'],
                strrpos($repositoryConfig['path'], '/') + 1,
                strrpos($repositoryConfig['path'], '.') - strrpos($repositoryConfig['path'], '/') - 1
            );
        }

        throw new \Exception('We can\'t make name for your repository ' . var_export($repository, true));
    }

    /**
     * Run step
     *
     * @param string $project
     * @param array  $projectConfig
     */
    abstract protected function _run(string $project, array $projectConfig);

}
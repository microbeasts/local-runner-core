<?php

namespace Microbeasts\LocalRunnerCore\Steps;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;

class CloneRepositories extends AbstractStep
{

    /**
     * Clone all repositories in a selected project
     *
     * @param string $project
     * @param array  $projectConfig
     *
     * @throws \Exception
     */
    protected function _run(string $project, array $projectConfig)
    {
        $repositories = $projectConfig['repositories'];

        if (!empty($repositories)) {
            foreach ($repositories as $repositoryKey => $repositoryConfig) {
                $repositoryDirectory = $this->makeRepositoryPath($project, [$repositoryKey => $repositoryConfig]);
                if (!is_dir($repositoryDirectory) && !empty($repositoryConfig['path'])) {
                    $processCloning = new Process($this->_makeCloningCommand($repositoryDirectory, $repositoryConfig['path'], $repositoryConfig['type']));
                    $processCloning->run();

                    foreach ($this->_makeAdditionalEnvVarsForComposerInstall($repositoryDirectory) as $env) {
                        putenv($env);
                    }

                    $input  = new ArrayInput(array('command' => 'install'));
                    $output = new BufferedOutput();
                    $application = new Application();
                    $application->setAutoExit(false);
                    $application->run($input, $output);
                }
            }
        }
    }

    /**
     * Make a command for cloning
     *
     * @param string $repositoryDirectory
     * @param string $repositoryPath
     * @param string $repositoryType
     * @return string
     *
     * @throws \Exception
     */
    private function _makeCloningCommand(string $repositoryDirectory, string $repositoryPath, string $repositoryType): string
    {
        switch ($repositoryType) {
            case 'git':
                return 'git clone ' . $repositoryPath . ' ' . $repositoryDirectory;
            default:
                throw new \Exception('We don\'t support ' . $repositoryType .' version control');
        }
    }

    /**
     * Make a command for right run 'composer install'
     *
     * @param string $repositoryDirectory
     * @return array
     */
    private function _makeAdditionalEnvVarsForComposerInstall(string $repositoryDirectory): array
    {
        return [
            'COMPOSER=' . $repositoryDirectory . '/composer.json',
            'COMPOSER_VENDOR_DIR=' . $repositoryDirectory . '/vendor',
        ];
    }

    /**
     * Make repository path
     *
     * @param string $project - project name
     * @param array $repository - name and link to repository
     * @return string
     */
    private function makeRepositoryPath(string $project, array $repository): string
    {
        return $this->getProjectPath($project) . '/' . $this->makeRepositoryName($repository);
    }

}
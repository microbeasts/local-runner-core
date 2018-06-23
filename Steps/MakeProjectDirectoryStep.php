<?php

namespace Microbeasts\LocalRunnerCore\Steps;

class MakeProjectDirectory extends AbstractStep
{
    /**
     * Make directory for new project
     *
     * @param string $project
     * @param array  $projectConfig
     */
    protected function _run(string $project, array $projectConfig)
    {
        $projectPath = $this->getProjectPath($project);
        if (!is_dir($projectPath)) {
            mkdir($projectPath);
        }
    }

}
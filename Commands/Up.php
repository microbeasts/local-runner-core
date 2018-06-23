<?php

namespace Microbeasts\LocalRunnerCore\Commands;

use Microbeasts\LocalRunnerCore\Steps\DownDockerCompose;
use Microbeasts\LocalRunnerCore\Steps\UpDockerComposeStep;
use Illuminate\Console\Command;

class Up extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locrun:up {project=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Up all containers in project';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $project = $this->argument('project');

        try {
            (new DownDockerCompose($project))->run();
            (new UpDockerComposeStep($project))->run();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}

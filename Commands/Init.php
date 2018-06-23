<?php

namespace Microbeasts\LocalRunnerCore\Commands;

use Microbeasts\LocalRunnerCore\Steps\BuildDockerComposeYml;
use Microbeasts\LocalRunnerCore\Steps\CloneRepositories;
use Microbeasts\LocalRunnerCore\Steps\MakeProjectDirectory;
use Illuminate\Console\Command;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locrun:init {project=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialization new project';

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
            (new MakeProjectDirectory($project))->run();
            (new CloneRepositories($project))->run();
            (new BuildDockerComposeYml($project))->run();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

}
<?php

namespace Microbeasts\LocalRunnerCore\Commands;

use Microbeasts\LocalRunnerCore\Steps\DownDockerCompose;
use Illuminate\Console\Command;

class Down extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locrun:down {project=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Down all containers in project';

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
            $this->info((new DownDockerCompose($project))->run());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}

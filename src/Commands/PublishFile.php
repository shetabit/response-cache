<?php

namespace Shetabit\ResponseCache\Commands;


use Illuminate\Console\Command;

class PublishFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes files package response caches';

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
        $this->callSilent('vendor:publish', ['--tag' => 'files' ,'--force' => true]);

        $this->info('Copied files successfully');
        $this->info('package was installed successfully.');
        $this->info("copy this to first line public/index.php");
        $this->info(" require_once __DIR__ . '/../app/Caches/LoadCache.php'; ");
    }
}

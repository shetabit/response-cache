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

        $this->info('Installing Package...');
        $this->info('Publishing configuration...');

        $this->callSilent('vendor:publish', ['--tag' => 'files' ,'--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'config' ,'--force' => true]);


        $content=file(public_path('index.php'));
        foreach($content as $lineNumber => &$lineContent) { //Loop through the array (the "lines")
            if($lineNumber == 1) { //Remember we start at line 0.
                $lineContent .= "require_once __DIR__ . '/../app/Caches/LoadCache.php';" . PHP_EOL; //Modify the line. (We're adding another line by using PHP_EOL)
            }
        }
        $allContent = implode("", $content);
        file_put_contents(public_path('index.php'), $allContent);


        $this->info('Installed Package');
        $this->info("copy this to first line public/index.php");
        $this->info(" require_once __DIR__ . '/../app/Caches/LoadCache.php'; ");
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\queuebackend\cron\ProcessQueueOperations;
use Illuminate\Console\Command;

class ProcessQueueOperationsCommand extends Command
{
    protected $signature = 'queue:process';

    protected $description = 'Process queue operations';

    public function handle()
    {
        (new ProcessQueueOperations())->processQueue();
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\queuebackend\cron\InRuleQueueOperations;
use Illuminate\Console\Command;

class InRuleQueueOperationsCommand extends Command
{
    protected $signature = 'queue:in';

    protected $description = 'Start queue operations';

    public function handle()
    {
        (new InRuleQueueOperations())->startQueue();
    }
}

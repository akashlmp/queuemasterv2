<?php

namespace App\Console\Commands;

use App\Http\Controllers\queuebackend\cron\OutRuleQueueOperations;
use Illuminate\Console\Command;

class OutRuleQueueOperationsCommand extends Command
{
    protected $signature = 'queue:out';

    protected $description = 'End queue operations';

    public function handle()
    {
        (new OutRuleQueueOperations())->endQueue();
    }
}
